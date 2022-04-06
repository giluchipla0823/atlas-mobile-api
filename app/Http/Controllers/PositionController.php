<?php

namespace App\Http\Controllers;

use App\Models\CompoundConfig;
use App\Models\Vehicle;
use Exception;
use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PositionController extends Controller
{
    /**
     * Obtener información de posición por su id.
     *
     * @param Position $position
     * @return JsonResponse
     */
    public function show(Position $position): JsonResponse
    {
        if ($position->type === Position::TYPE_ROW) {
            $position = $position->load(['children', 'children.vehicle', 'children.vehicle.design', 'children.vehicle.color']);
        }

        return response()->json(['error' => false, 'data' => $position]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function rowInfo(Request $request): JsonResponse {
        $rowLp = $request->rowLp;

        $position = Position::where(function ($query) use ($rowLp) {
            $query->where('lp', '=' , $rowLp)
                ->orWhere('alt_qr', '=', $rowLp);
        })->first();

        if(!$position){
            throw new Exception('La posición especificada no existe.', Response::HTTP_BAD_REQUEST);
        }

        $position->state = (int) $position->active === 1;

        return response()->json(['error' => false, 'data' => $position]);
    }

    /**
     * Habilitar o deshabilitar posición.
     *
     * @param Position $position
     * @return JsonResponse
     */
    public function lockOrUnlock(Position $position): JsonResponse {
        $active = $position->active ? 0 : 1;
        $position->active = $active;
        $position->save();

        if ($active === 1) {
            $message = 'La fila se habilitó correctamente.';
        } else {
            $message = 'La fila se deshabilitó correctamente.';
        }

        return response()->json(['error' => false, 'message' => $message]);
    }

    /**
     * Obtener las posiciones hijas de una posición especificada.
     *
     * @param Position $position
     * @return JsonResponse
     */
    public function children(Position $position): JsonResponse
    {
        $positions = $position->children()->with(['children'])->get();

        return response()->json($positions);
    }

    /**
     * Activar o desactivar fila.
     *
     * @param Position $position
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function toggleRow(Position $position, Request $request): JsonResponse
    {
        if ($position->type !== Position::TYPE_ROW) {
            throw new Exception('La posición especificada no es una fila.', Response::HTTP_BAD_REQUEST);
        }

        $position->comment = $request->has('message') ? $request->message : null;

        $position->toggleActive();

        if ((int) $position->active === 1) {
            $message = 'La fila se habilitó correctamente.';
        } else {
            $message = 'La fila se ha bloqueado correctamente.';
        }

        return response()->json([
            'error' => false,
            'message' => $message,
            'position' => $position
        ]);
    }

    /**
     * Obtener posiciones directas para "BACK TO PLANT".
     *
     * @param Position $compound
     * @return JsonResponse
     * @throws Exception
     */
    public function getPositionsForBackToPlant(Position $compound): JsonResponse
    {
        $plantPositionId = $compound->singleConfig(CompoundConfig::PARAMETER_POSITION_PLANT);

        if (!$plantPositionId) {
            throw new Exception(
                'El sistema no tiene configurado una campa de tipo PLANTA - FÁBRICA para la campa actual.',
                Response::HTTP_BAD_REQUEST
            );
        }

        // Obtener los parking de la planta fábrica
        $positions = Position::where('parent', $plantPositionId)->where([
            'direct_position' => Position::DIRECT_POSITION_ON,
            'type' => Position::TYPE_PARKING
        ])->get();

        $result = [];

        $chunks = $positions->chunk(2);

        foreach ($chunks as $chunk) {
            $result[] = $chunk->values();
        }

        return response()->json(['error' => false, 'data' => $result]);
    }

    /**
     * Obtener campa de tipo "Planta - fabrica" que tiene asignada una campa.
     *
     * @param Position $compound
     * @return JsonResponse
     * @throws Exception
     */
    public function getCompoundPlantType(Position $compound): JsonResponse
    {
        $positionId = $compound->singleConfig(CompoundConfig::PARAMETER_POSITION_PLANT);

        if (!$position = Position::find($positionId)) {
            throw new Exception(
                'La campa actual no tiene asignada una campa de tipo planta.',
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(['error' => false, 'data' => $position]);
    }

    /**
     * Obtener campa de tipo "Planta - Buffer" que tiene asignada una campa.
     *
     * @param Position $compound
     * @return JsonResponse
     * @throws Exception
     */
    public function getCompoundPlantBuffer(Position $compound): JsonResponse
    {
        $positionId = $compound->singleConfig(CompoundConfig::PARAMETER_POSITION_BUFFER);

        if (!$position = Position::find($positionId)) {
            throw new Exception(
                'La campa actual no tiene asignada una campa de tipo buffer.',
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(['error' => false, 'data' => $position]);
    }

    /**
     * Obtener último vehículo posicionado de la fila.
     *
     * @param Position $position
     * @return JsonResponse
     * @throws Exception
     */
    public function getLastVehiclePositioned(Position $position): JsonResponse
    {
        if ($position->type !== Position::TYPE_ROW) {
            throw new Exception('La posición seleccionada debe ser una fila.', Response::HTTP_BAD_REQUEST);
        }

        $slots = $position->children()->with(['vehicle'])->get();

        if ($slots->count() === 0) {
            throw new Exception('La fila seleccionada no tiene slots asignados.', Response::HTTP_BAD_REQUEST);
        }

        $filledSlots = $slots->filter(function($slot) {
            return $slot->fill === 1 && !is_null($slot->vehicle);
        });

        $countFilledSlots = $filledSlots->count();

        if ($countFilledSlots === 0) {
            throw new Exception('La fila seleccionada no tiene vehículos asignados.', Response::HTTP_BAD_REQUEST);
        }

        $lastFilledSlot = $filledSlots->last();

        $vehicle = $lastFilledSlot->vehicle()->with(['color', 'design'])->first();
        $vehicle->row_position = $position;
        $vehicle->slot_number = $countFilledSlots;

        return response()->json($vehicle);
    }

    /**
     * Pruebas para obtener slot disponible de una fila.
     *
     * @param Position $position
     * @return JsonResponse
     * @throws Exception
     */
    public function testSlotAvailable(Position $position): JsonResponse
    {
        $slot = $position->slotAvailable();

        return response()->json(['error' => false, 'data' => $slot]);
    }

    /**
     * Row Rellocate.
     *
     * @param Position $position
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function rowRellocate(Position $position, Request $request): JsonResponse
    {
        $row = $position;

        if ($row->type !== Position::TYPE_ROW) {
            throw new Exception("La posición con identificador {$row->id} debe ser de tipo Fila.", Response::HTTP_BAD_REQUEST);
        }

        $compoundId = auth()->user()->parent_compound;

        if (!$compound = Position::find($compoundId)) {
            throw new Exception(
                'La posición de la campa no existe.',
                Response::HTTP_NOT_FOUND
            );
        }

        $bufferId =  $compound->singleConfig(CompoundConfig::PARAMETER_POSITION_BUFFER);

        /* @var Position $buffer */
        if (!$buffer = Position::find($bufferId)) {
            throw new Exception(
                'La campa actual no tiene asignada una campa de tipo buffer.',
                Response::HTTP_NOT_FOUND
            );
        }

        $vehiclesToRow = $request->get('row_vehicles', []);
        $vehiclesToBuffer = $request->get('buffer_vehicles', []);
        $vehicleCollectionToBuffer = Vehicle::with(['design'])
            ->whereIn('id', array_column($vehiclesToBuffer, 'id'))
            ->get();

        $currentSlots = $row->children()->with(['vehicle', 'vehicle.design'])->get();
        $usedSlots = $currentSlots->filter(function($slot) {
            return $slot->fill === 1 && !is_null($slot->vehicle);
        });

        DB::beginTransaction();

        try {

            // Liberar espacio en la fila
            /* @var Position $slot */
            foreach ($usedSlots as $slot) {
                $vehicle = $slot->vehicle()->first();

                $slot->release($vehicle->design->length);
            }

            /**
             * Asignar vehículos a la fila.
             * Liberar espacio de la posición origen del vehículo.
             * LLenar espacio en la fila.
             *
             */
            foreach ($vehiclesToRow as $vehicle) {
                $id = $vehicle['id'];
                $originPositionId = (int) $vehicle['position_from'];
                $finalPositionId = (int) $vehicle['position_to'];
                $vehicle = Vehicle::find($id);
                $length = $vehicle->design()->first()->length;

                if ($originPositionId !== $finalPositionId) {
                    /* @var Position $originPosition */
                    $originPosition = Position::find($originPositionId);
                    $originPosition->liberar($length);
                }

                /* @var Position $finalPosition */
                $finalPosition = Position::find($finalPositionId);
                $finalPosition->ocupar($length);

                $vehicle->position_id = $finalPosition->id;
                $vehicle->save();
            }

            // Asignar vehículos a Zona de Buffer.
            foreach ($vehicleCollectionToBuffer as $vehicle) {
                $buffer->ocupar($vehicle->design->length);

                $vehicle->position_id = $buffer->id;
                $vehicle->save();
            }

            DB::commit();
        } catch (Exception $exc) {
            DB::rollback();

            throw $exc;
        }

        return response()->json([
            'error' => false,
            'message' => 'La fila se ha reubicado correctamente.'
        ]);
    }
}
