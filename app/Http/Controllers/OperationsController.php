<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Position;
use App\Models\Vehicle;
use App\Models\Design;

class OperationsController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleRow(Request $request): JsonResponse
    {
        $position = Position::find($request->positionId);

        if($request->has('message')) {
            $position->comment = $request->message;
        }
        else {
            $position->comment = NULL;
        }

        return response()->json(['result' => $position->toggleActive() === 1]);
    }

    /**
     * Limpiar fila cuando es Rellocation.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function clearRowRellocation(Request $request): JsonResponse
    {

        // Posición a vaciar
        $row = Position::find($request->rowId);

        // Vehículo en pos 1
        $vehicle = Vehicle::find($request->vehicleId);

        // Liberar posición actual de vehículo
        Position::find($vehicle->position_id)->release($vehicle->design->length);

        // Liberación de posiciones (slots) en esta fila
        $slots = $row->children;

        foreach ($slots as $slotSingle) {
            $slot = Position::find($slotSingle->id);

            if ($slot->id != $vehicle->position_id) {
                $slot->release($vehicle->design->length);
            }
        }

        //Vaciado de fila nueva
        $row->fill = 0;
        $row->fillmm = 0;
        $row->category = $vehicle->category->name;
        $row->save();

        //Reserva de la primera posición con el vehículo leído
        $reserve = $row->reserve($vehicle->design->length, $vehicle->category->name);
        $vehicle->position_id = $reserve->id;

        // Recogida de la fila actual para responder
        $row->fresh();

        return response()->json(['error'=>!$vehicle->save(),'data'=>$reserve]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function vehicleRowRellocation(Request $request): JsonResponse
    {

        $minLength = Design::min('length');

        $vehicle = Vehicle::find($request->vehicleId);
        $position = Position::find($request->rowId);

        $reserve = $position->reserve($vehicle->design->length,$vehicle->category->name);
        $position = $position->flush();
        $vehicle->position_id = $reserve->id;

        $reserve->full = ($position->capacitymm - $position->fillmm) < $minLength;

        if ($reserve->full) {
            $position->createLoad();
        }

        return response()->json(['error' => !$vehicle->save(),'data' => $reserve]);
    }

    public function vehicleSingleRellocation(Request $request){

        $vehicle = Vehicle::find($request->vehicleId);
        $position = Position::find($request->positionId);

        if($position->type == 2)
            $reserve = $position->reserve($vehicle->design->length,$vehicle->category->name);
        else{
            $dt= new \DateTime();
            $reserve = $position->placeVehicle($vehicle,$vehicle->category,$request->userId,$dt);
        }

        Position::find($vehicle->position_id)->release($vehicle->design->length);

        $vehicle->position_id = $reserve->id;
        $vehicle->state_id = $request->nextState;

        return array('error'=>!$vehicle->save(),'data'=>$reserve);
    }
}
