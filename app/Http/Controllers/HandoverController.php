<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HandoverController extends Controller{

    /**
     * TODO: REVISAR ESTA FUNCIONALIDAD
     * Receive vehicle.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function receive(Request $request): JsonResponse
    {
        if (!$vehicle = Vehicle::find($request->vehicleId)) {
            throw new Exception("El vehículo con el id {$request->vehicleId} no existe.", Response::HTTP_NOT_FOUND);
        }

        $receive = $vehicle->receive($request->compoundId, $request->type);

        return response()->json(['error' => !$receive, 'message'=>'Vehicle received']);
    }

    public function registerVin(Request $request){

        try {
            //code...
            $vehicle = new Vehicle();

            $vehicle->vin = $request->vin;
            $vehicle->vin_short = substr($request->vin,-7);
            $vehicle->country_id = $request->countryId;
            $vehicle->design_id = $request->modelId;
            $vehicle->color_id = $request->colorId;
            $vehicle->position_id = $request->positionId;
            $vehicle->parent_compound = $request->compundId;
            $vehicle->state_id = 1;
            $vehicle->EOC = '';
            $vehicle->last_rule_id = NULL;
            $vehicle->shipping_rule_id = NULL;
            $vehicle->vin = $request->vin;

            return array('error'=>!$vehicle->save(), 'data'=>$vehicle);
        } catch (\Throwable $th) {
            return array('error'=>true,'message'=>$th);
        }

    }
}
