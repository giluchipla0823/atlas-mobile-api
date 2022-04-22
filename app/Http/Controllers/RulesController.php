<?php

namespace App\Http\Controllers;

use App\Http\Requests\Position\PositionRecommendRequest;
use App\Http\Requests\Position\PositionReloadRequest;
use App\Models\State;
use Exception;
use App\Services\Application\RuleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Relevant;
use App\Models\Rule;
use App\Models\Design;
use App\Models\Distance;
use App\Models\Position;
use App\Models\CompoundConfig;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RulesController extends Controller
{

    /**
     * @var RuleService
     */
    private $ruleService;

    public function __construct(RuleService $ruleService)
    {
        $this->ruleService = $ruleService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $rules = $this->ruleService->findAll($request);

        return response()->json($rules);
    }

    /**
     * Recomendar posición.
     *
     * @param PositionRecommendRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function recommend(PositionRecommendRequest $request): JsonResponse
    {
        $vehicle = Vehicle::find($request->vehicleId);

        DB::beginTransaction();

        try {
            // Recepción
            if (!$vehicle->state_id) {
                $vehicle->receive();
            }

            $vehicleInfo = $vehicle->info();
            $relevants = Relevant::get();
            $vehicleSet = [];

            /**
             * Creación de la estructura para comparar reglas.
             */
            foreach ($relevants as $key => $relevant) {
                try {
                    $vehicleSet[$relevant->id] = $vehicleInfo->{$relevant->parameter_vin}->name;
                } catch (\Throwable $th) {
                    foreach (array_keys($vehicle->getAttributes()) as $attribute){
                        try {
                            $vehicleSet[$relevant->id] = $vehicleInfo->{str_replace('_id','',$attribute)}->{$relevant->parameter_vin}->name; //PROBABLEMENTE CAMBIE POR name AL REFERIRSE POR VALUE Y NO ID
                        } catch (\Throwable $th) {
                            $vehicleSet[$relevant->id] = NULL;
                        }
                    }
                }
            }

            // Reglas solo con origen actual
            $allRules = Rule::where('origin_block_id', $vehicle->position->block_id)->get()->map(function($rule) {
                return $rule->ruleinfo();
            });

            // Comparación reglas con vehículo.
            foreach ($allRules as $rule) {
                $rule->score = $this->compare($vehicleSet, $rule);
            }

            // Eliminación de puntuaciones negativas.
            $allRules = $allRules->filter(function($rule) {
                return $rule->score > 0;
            });

            // Ordenado por puntuación y prioridad.
            $allRules = $allRules->sortBydesc('score')->sortBy('priority');

            // Obtener la primera regla configurada.
            $selectedRule = $allRules->first();

            // dd($selectedRule);

            // Si no hay regla -> YARD
            if (!$selectedRule) {
                $defaultOverflowPosition = CompoundConfig::where('compound_id', $vehicle->parent_compound)
                    ->where('parameter', CompoundConfig::PARAMETER_DEFAULT_OVERFLOW)
                    ->first()->value;

                $destination = Position::find($defaultOverflowPosition);
                $destination->direct = true;

                $this->changeDrivenStateVehicle($vehicle, $destination);

                return response()->json(['error' => false, 'position' => $destination]);
            }

            /**
             * Si la regla tiene una posición directa se recoge la posición directamente (zonas sin filas).
             *
             */
            if($selectedRule->direct) {
                $destination = $selectedRule->directZone;
                $destination->increment('fill');
                $destination->increment('fillmm', $vehicleInfo->design->length);
                $destination->direct = true;

                $this->changeDrivenStateVehicle($vehicle, $destination);

                return response()->json(['error' => false, 'position' => $destination]);
            }

            /**
             * Posiciones con filas.
             */
            if ($selectedRule->predefined_zone) { // Zona predefinida por usuario
                $positions = $selectedRule->block->positions->where('parent', $selectedRule->predefined_zone);
            } else { // Lista de posibles filas
                $positions = $selectedRule->block->positions->where('type', Position::TYPE_ROW);
            }

            /**
             * Búsqueda de fila ya abierta de la regla a aplicar, en caso contrario buscar vacía más cercana.
             *
             */
            // $opened = $positions->firstWhere('category', $selectedRule->name);
            $opened = $positions->where('category', '=', $selectedRule->name)
                ->filter(function($position) {
                    return $position->fill < $position->capacity;
                })
                ->first();

            // dd($opened);

            if ($opened) {
                // $destination = $opened;

                // $opened->decrement('fill');
                // $destination = $opened;
                // $destination = $opened->reserve($vehicleInfo->design->length, $selectedRule->name);
                $destination = $opened->slotAvailable();
                // $destination->direct = false;
            } else {

                // Búsqueda de fila más cercana

                $originPositionId = $vehicle->position->id;

                if (intval($vehicle->position->type) === Position::TYPE_SLOT) {
                    $originPositionId = $vehicle->position->parent;
                }

                /**
                 * TODO: VERIFICAR ESTA FUNCIONALIDAD DISTANCIAS
                 * Búsqueda de fila más cercana.
                 */
                $distanceRegister = Distance::leftJoin('positions', 'distances.destination_id', '=', 'positions.id')
                    // ->where('distances.origin_id', $vehicle->position->parent)
                    ->where('distances.origin_id', $originPositionId)
                    ->whereRaw('positions.fill < positions.capacity')
                    ->orderBy('seconds')
                    ->first();

                $destinationLane = $distanceRegister ? $distanceRegister->destination : null;

                if($destinationLane){
                    $destination = $destinationLane->reserve($vehicleInfo->design->length, $selectedRule->name);
                    $destination->direct = false;
                }else{
                    $destination = Position::find($selectedRule->overflow_id);
                    $destination->direct = true;
                }
            }

            dd($destination);

            $destination->ruleId = $selectedRule->id;
            $destination->nextState = $selectedRule->next_state;

            $this->changeDrivenStateVehicle($vehicle, $destination);

            DB::commit();

            return response()->json(['error' => false, 'position' => $destination]);
        } catch (Exception $exc) {
            DB::rollback();

            throw $exc;
        }
    }

    /**
     * @param Vehicle $vehicle
     * @param Position $destination
     */
    private function changeDrivenStateVehicle(Vehicle $vehicle, Position $destination): void
    {
        // Formato correcto Lpname
        $lparr = explode('.',$destination->lpname);
        array_shift($lparr);
        $lpname = implode('.',$lparr);

        // Ponemos vehículo en estado Driven
        $vehicle->state_id = State::STATE_ID_DRIVEN;
        $vehicle->on_route = $lpname;
        $vehicle->save();
    }

    private function compare($vehSet, $rule)
    {
        $ruleSet = [];

        // Para cada regla coger sus definiciones y compararla con las definiciones del vehículo
        foreach ($rule->definitions as $def) {
            $ruleSet[$def->relevant_id] = $def->value;
        }

        // Devuelve score de ese par clave:valor
        return count($vehSet) - count(array_diff($vehSet, $ruleSet));
    }

    /**
     * Reload Position.
     *
     * @param PositionReloadRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function reload(PositionReloadRequest $request): JsonResponse
    {
        $vehicle = Vehicle::find($request->vehicleId);
        $rule = Rule::find($request->ruleId);
        $vehicleInfo = $vehicle->info();
        $visited = json_decode($request->visited);

        // TODO: Verificar si es necesario hacer esto Release
        // Position::find($request->originalPositionId)->release($vehicle->design->length);

        $distanceRegister = Distance::leftJoin('positions', 'distances.destination_id', '=', 'positions.id')
            ->where('distances.origin_id', $vehicle->position->parent)
            ->whereRaw('positions.fill < positions.capacity')
            ->whereNotIn('positions.id', $visited)
            ->orderBy('seconds')
            ->first();

        $destinationLane = $distanceRegister ? $distanceRegister->destination : null;

        if($destinationLane) {

            $destination = $destinationLane->reserve($vehicleInfo->design->length, $rule->name);
            $destination->direct = false;

            if ($destinationLane->row_type == 0) {
                $visited[] = $destinationLane->id; // Añadir FILA a visitados
            }
            else {
                $visited[] = $destination->id; // Añadir SLOT a visitados (espigas)
            }

            $destination->ruleId = $rule->id;
            $destination->nextState = $rule->next_state;
        }
        else{
            $destination = Position::find($rule->overflow_id);
            $destination->direct = true;

            /**
             * TODO: Si es parking:
             * - Debe verificar si tiene filas asignadas.
             * - Si tiene filas asignadas, debe recomendarme una fila que tenga slots disponibles. Adicional a ello
             * tener en cuenta aquellas que ya han sido visitadas.
             *
             */
            switch ($destination->type) {
                case Position::TYPE_PARKING:

                    // Filas con espacio del parking.
                    $rows = $destination->children->whereNotIn('id', $visited)->filter(function($row) {
                        return $row->capacity > $row->fill;
                    })->values();

                    $row = $rows->first();

                    if ($row && $slot = $row->slotAvailable()) {
                        if (!in_array($row->id, $visited)) {
                            $visited[] = $row->id;
                        }

                        $destination = $slot;
                        $destination->direct = false;
                    }

                    break;
                case Position::TYPE_ROW:
                    if (!in_array($destination->id, $visited)) {
                        $visited[] = $destination->id;
                    }

                    $slot = $destination->slotAvailable();

                    if ($slot) {
                        $destination = $slot;
                        $destination->direct = false;
                    }
                    break;
                default:
                    break;
            }

            $destination->ruleId = $rule->id;
            $destination->nextState = $rule->next_state;
        }

        return response()->json([
            'error' => false,
            'position' => $destination,
            'visited' => $visited
        ]);
    }
}
