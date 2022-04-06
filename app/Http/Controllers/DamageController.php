<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Damage;
use App\Models\DamageType;
use App\Models\DamageSeverity;
use App\Models\DamagePortion;
use App\Models\DamageComponent;

class DamageController extends Controller
{
    public function saveDamage(Request $request){

        foreach ($request->damages as $damageInput) {
            $damage = new Damage;
    
            $damage->vehicle_id = $request->vehicleId;
            $damage->portion_id = $damageInput->portionId;
            $damage->severity_id = $damageInput->severityId;
            $damage->type_id = $damageInput->typeId;
            $damage->portion_id = $damageInput->portionId;

            $damage->save();
        }

    }

    public function getDmgType(Request $request){
        return DamageType::where('active',1)->get();
    }

    public function getDmgPortion(Request $request){
        return DamagePortion::where('active',1)->get();
    }

    public function getDmgSeverity(Request $request){
        return DamageSeverity::where('active',1)->get();
    }

    public function getDmgComponents(Request $request){
        
        $components = DamageComponent::where('compound_id',$request->compoundId)->get();

        $pair = array();
        $pairs = array();

        for ($i=1; $i <= count($components) ; $i++) { 
            
            $pair[] = $components[$i-1];

            if($i%3 == 0 || ($i%3 != 0 && $i == count($components))){
                $pairs[] = $pair;
                $pair = array();
            }
        }
        
        return $pairs;
    }
}
