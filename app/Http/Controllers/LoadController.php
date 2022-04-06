<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Load;
use App\Models\Vehicle;

class LoadController extends Controller
{
    public function createManualLoad(Request $request){

        $load =  new Load;

        $load->carrier_id = $request->carrierId;
        $load->rule_id = $request->ruleId;
        $load->code = 'CPS-LV-5467';
        $load->parent_compound = $request->compoundId;
        $load-> save();
        $load->notify(count($request->vehicles),$request->user,$request->compoundId);

        foreach ($request->vehicles as $vehicleReq){

            $vehicle = Vehicle::find($vehicleReq['vehicle']['id']);

            $vehicle->load_id = $load->id;
            $vehicle->save();
        }

        return array('error'=>false, 'data'=>$load);

    }

    public function createLoadOnAsset(Request $request){

        $load =  new Load;

        $load->carrier_id = $request->carrierId;
        $load->rule_id = $request->ruleId;
        $load->code = 'someTest';
        $load->asset_code = $request->assetCode;
        $load->parent_compound = $request->compoundId;
        $load-> save();
        $load->notify(count($request->vehicles),$request->user,$request->compoundId);

        foreach ($request->vehicles as $vehicleReq){

            $vehicle = Vehicle::find($vehicleReq['vehicle']['id']);

            $vehicle->load_id = $load->id;
            $vehicle->save();
        }

        return array('error'=>false, 'data'=>$load);

    }

    
}
