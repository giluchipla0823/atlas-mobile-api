<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movement\MovementCancelRequest;
use App\Http\Requests\Movement\MovementConfirmRequest;
use App\Http\Requests\Movement\MovementManualRequest;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Position;
use App\Models\Vehicle;
use App\Models\Distance;
use App\Models\DistanceLog;
use App\Models\Movement;
use App\Models\Rule;
use App\Models\Design;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class MovementController extends Controller
{
    /**
     * Confirmar posición final.
     *
     * @param MovementConfirmRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function confirm(MovementConfirmRequest $request): JsonResponse
    {
        $vehicleId = $request->vehicleId;
        $positionId = $request->positionId;
        $originalPositionId = $request->originalPositionId;
        $ruleId = $request->ruleId;
        $userId = auth()->user()->id;
        $dtOrigin = $request->dtOrigin;

        $finalPosition = Position::find($positionId);

        if (!$finalPosition) {
            throw new Exception('Non existing position defined.', Response::HTTP_BAD_REQUEST);
        }


//        $vehicle = Vehicle::find($vehicleId);
//
//        // Liberar espacio posicion normal
//        $originalPosition = Position::find($originalPositionId);
//        $originalPosition->release($vehicle->design->length);
//
//        // Sumatoria posición final
//        $finalPosition->sumatoria($vehicle);
//
//        dd('hola');



        DB::beginTransaction();

        try {



            // Guardar información de vehículo.
            $vehicle = Vehicle::find($vehicleId);
            $vehicle->position_id = $finalPosition->id;
            $vehicle->state_id = $request->nextState;
            $vehicle->on_route = null;
            $vehicle->save();

    //        // Grabar movimiento
    //        $movement = new Movement;
    //        $movement->parent_compound = $finalPosition->parent_compound;
    //        $movement->vehicle_id = $vehicle->id;
    //        $movement->user_id = $userId;
    //        $movement->origin_id = $originalPositionId;
    //        $movement->destination_id = $positionId;
    //        $movement->rule_id = $ruleId;
    //        $movement->dt_start = Carbon::parse($dtOrigin);
    //        $movement->dt_end = Carbon::now();
    //        $movement->save();

            $originalPosition = Position::find($originalPositionId);
            // $originalPosition->release($vehicle->design->length);
            $originalPosition->liberar($vehicle->design->length);

//            /**
//             * TODO: Revisar esta funcionalidad de distancias
//             * Recogida media actual
//             */
//            $averageSeconds = intval(DistanceLog::where([
//                'origin_id' => $originalPosition->id, // La posición original de donde viene el coche
//                'destination_id' => $finalPosition->parent // La fila del slot donde se está reubicando el coche.
//            ])->avg('seconds'));
//
//            if ($averageSeconds) {
//                $seconds = intval(Carbon::now()->diff(Carbon::parse($dtOrigin))->format('s'));
//
//                // Guardar log de distancias.
//                $distanceLog = new DistanceLog();
//                $distanceLog->origin = $originalPosition->id;
//                $distanceLog->destination = $finalPosition->id;
//
//                $distanceLog->seconds = $seconds;
//
//                // Actualizar información de distancia.
//                $distance = Distance::where([
//                    'origin_id' => $originalPosition->id,
//                    'destination_id' => $finalPosition->parent
//                ])->first();
//
//                $distance->seconds = ($seconds + $averageSeconds) / 2;
//                $distance->save();
//            }



            /**
             * TODO: Revisar esta funcionalidad de distancias
             * Recogida media actual
             */
            $averageSeconds = intval(Distance::where([
                'origin_id' => $originalPosition->id, // La posición original de donde viene el coche
                'destination_id' => $finalPosition->parent // La fila del slot donde se está reubicando el coche.
            ])->avg('seconds'));

            if ($averageSeconds > 0) {
                $seconds = intval(Carbon::now()->diff(Carbon::parse($dtOrigin))->format('s'));

                // dd($seconds);

//                // Guardar log de distancias.
//                $distanceLog = new DistanceLog();
//                $distanceLog->origin = $originalPosition->id;
//                $distanceLog->destination = $finalPosition->id;
//
//                $distanceLog->seconds = $seconds;

                // Actualizar información de distancia.
//                $distance = Distance::where([
//                    'origin_id' => $originalPosition->id,
//                    'destination_id' => $finalPosition->parent
//                ])->first();

                $distance = new Distance();
                $distance->origin_id = $originalPosition->id;
                $distance->destination_id = $finalPosition->parent;
                $distance->seconds = ($seconds + $averageSeconds) / 2;
                $distance->save();
            } else {
                /**
                 * Si no tengo respuesta de distancia por el origen y destino de la posición
                 * debo realizar un primer registro
                 */

                // $seconds = intval(Carbon::now()->diff(Carbon::parse($dtOrigin))->format('s'));
                $seconds = 20;

                $distance = new Distance();
                $distance->origin_id = $originalPosition->id;
                $distance->destination_id = $finalPosition->parent;
                $distance->seconds = $seconds;
                $distance->save();
            }

            // throw new Exception('Error de mierda', 400);

            $rule = Rule::find($ruleId);

            // Guardar movimiento
            $finalPosition->placeVehicle2(
                $vehicle,
                $originalPosition->id,
                $finalPosition->id,
                $rule,
                $userId,
                new DateTime()
            );

            // Comprobar llenado de cargas
            if ($finalPosition->type === Position::TYPE_SLOT) {

                $minLength = Design::min('length');
                $row = $finalPosition->parent()->first();

                if(($row->capacitymm - $row->fillmm) < $minLength || $row->capacity == $row->fill) {
                    $row->createLoad();
                }
            }

            DB::commit();

        } catch (Exception $exc) {
            DB::rollback();

            throw $exc;
        }

        return response()->json(['error' => false, 'data' => $finalPosition]);
    }

    /**
     * Cancelar moviemiento.
     *
     * @param MovementCancelRequest $request
     * @return JsonResponse
     */
    public function cancel(MovementCancelRequest $request): JsonResponse
    {
        $vehicle = Vehicle::find($request->vehicleId);
        $vehicle->on_route = NULL;
        $vehicle->save();

        $release = Position::find($request->positionId)->release($vehicle->design->length);

        return response()->json(['error'=> !$release]);
    }

    /**
     * @param MovementManualRequest $request
     * @return JsonResponse
     */
    public function manual(MovementManualRequest $request): JsonResponse
    {
        $minLength = Design::min('length');
        $vehicle = Vehicle::find($request->vehicleId);
        $position = Position::find($request->positionId);

        // Guardar movimiento para nueva posición.
        $position->placeVehicle($vehicle, $request->ruleId, $request->userId, new DateTime());

        // Liberar espacio de posición original.
        Position::find($request->originalPositionId)->release($vehicle->design->length);

        // Actualizar información del vehículo.
        $vehicle->position_id = $request->positionId;
        $vehicle->state_id = $request->nextState;
        $vehicle->on_route = NULL;
        $vehicle->save();

        // Si es slot, comprobar llenado de su fila
        if((int) $position->type === Position::TYPE_SLOT){

            // Get fila
            $row = $position->parent->first();

            // Si es fila y está llena, crear carga
            if(
                (($row->capacitymm - $row->fillmm) < $minLength || $row->capacity == $row->fill) &&
                $row->row_type !== Position::ROW_TYPE_ESPIGA
            ) {
                $row->createLoad();
            }
        }

        // $vehicle->save();
        // $position = Position::find($request->positionId);

        return response()->json(['error' => false, 'data' => $position]);

        // return array('error'=>!$vehicle->save(),'data'=>Position::find($request->positionId));
    }

//    /**
//     * Obtener posiciones mediante el campo parent.
//     *
//     * @param Request $request
//     * @return array
//     */


    // public function children(Request $request): JsonResponse
    public function children(Position $position): JsonResponse
    {
        // $position = Position::find($request->positionId);

        // dd($position->children()->get());

        $positions = $position->children()->get();

        return response()->json($positions);


        // return array('list'=>Position::find($request->positionId)->children->get());
    }

    public function rowInfo(Request $request): JsonResponse
    {
        try {
            $position = Position::find($request->positionId);

            return response()->json(['error' => false, 'position' => $position]);

            // return array('error'=>false,'position' => $position);
        } catch (Exception $exc) {
            // return array('error'=>true,'message'=>$th);

            return response()->json(['error' => true, 'message' => $exc->getMessage()]);
        }
    }

//    /**
//     * Siguiente Slot.
//     *
//     * @param Request $request
//     * @return JsonResponse
//     * @throws Exception
//     */
//    public function nextSlot(Request $request): JsonResponse
//    {
//        $position = Position::find($request->positionId);
//
//        if (!$position) {
//            throw new Exception("La posición especificada no existe.", Response::HTTP_NOT_FOUND);
//        }
//
//        if (!$request->has('length')) {
//            throw new Exception("Debe especificar la longitud del diseño del vehículo.", Response::HTTP_BAD_REQUEST);
//        }
//
//        // $position->release($request->length);
//
//        // dd($position->nextPosition());
//
//        // $nextPosition = $position->nextPosition->first();
//
//        // return response()->json($nextPosition);
//
//        $nextPosition = Position::find($request->positionId);
//
//        return response()->json($position);
//    }


    /**
     * Next Slot.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function nextSlot(Request $request): JsonResponse
    {
        $position = Position::find($request->positionId);

        if (!$position) {
            throw new Exception("La posición especificada no existe.", Response::HTTP_NOT_FOUND);
        }

//        if (!$request->has('length')) {
//            throw new Exception("Debe especificar la longitud del diseño del vehículo.", Response::HTTP_BAD_REQUEST);
//        }

        if (!$position->next) {
            throw new Exception('La posición actual no tiene definida una posición siguiente.', Response::HTTP_BAD_REQUEST);
        }

        $nextPosition = Position::find($position->next);

        if (!$nextPosition) {
            throw new Exception("No se encontró información de la posición siguiente especificada.", Response::HTTP_NOT_FOUND);
        }

        // DUDA: ¿Liberar espacio?
        // $position->release($request->length);

        return response()->json($nextPosition);
    }

    /**
     * Lista de vehículos cercanos a la posición actual en modalidad Espiga.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function fishboneLayout(Request $request): JsonResponse
    {
        if (!$position = Position::find($request->positionId)) {
            throw new Exception('No se encontró información de la posición seleccionada.', Response::HTTP_NOT_FOUND);
        }

        $result = $position->fishboneAroundPosition();

        return response()->json($result);
    }

    /**
     * Primer carril correcto.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function correctFirstLane(Request $request): JsonResponse
    {
        $length = $request->length;

        // Liberar antigua posición
        Position::find($request->originalPositionId)->release($length);

        // Reservar actual (corrección)
        $position = Position::find($request->positionId)->reserve($length, $request->ruleName);

        return response()->json($position);
    }
}
