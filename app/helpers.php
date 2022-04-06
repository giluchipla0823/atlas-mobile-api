<?php

use App\Models\Relevant;
use App\Models\Rule;
use App\Models\Design;
use App\Models\Distance;

function shipping($vehicle){
    
    $minLength = Design::min('length');

    //asignar estado previo a Ready y parking fantasma pre-ready
    $vehicle->state_id = 4;
    $vehicle->position_id = 999;

    //Recepción
    if(!$vehicle->state_id)
        $vehicle->receive();

    $vehicleInfo = $vehicle->info();
    $relevants = Relevant::get();

    foreach ($relevants as $key => $relevant) { //Creación de la estructura para comparar reglas   
        try {
            $vehicleSet[$relevant->id] = $vehicleInfo->{$relevant->parameter}->name;
            // $vehicleSet[$relevant->id] = $vehicleInfo->{$relevant->parameter}->id;
        } catch (\Throwable $th) {                
            foreach (array_keys($vehicle->getAttributes()) as $attribute){
            try {
                    $vehicleSet[$relevant->id] = $vehicleInfo->{str_replace('_id','',$attribute)}->{$relevant->parameter}->id;
                } catch (\Throwable $th) {
                    $vehicleSet[$relevant->id] = NULL;
                }
            }
        }            
    }

    //Recoger reglas que dejan en estado Ready
    $allRules = Rule::get()->where('next_state',5)->map(function($rule) {
        return $rule->ruleinfo();
    });

    foreach ($allRules as $key => $rule) { //Comparación reglas con vehículo
        $rule->score = compare($vehicleSet,$rule,$relevants);
    }

    $allRules->map(function($rule){ //Eliminación de puntuaciones negativas
        return $rule->score > 0;
    });

    $allRules->sortBydesc('score')->sortBy('priority'); //Ordenado por puntuación y prioridad
    $selectedRule = $allRules[0];

    if($selectedRule->direct){ //Si la regla tiene una posición directa se recoge la posición directamente (zonas sin filas)
        $destination = $selectedRule->directZone;

        $destination->increment('fill');
        $destination->increment('fillmm',$vehicleInfo->design->length);
    }
    else{ //Posiciones con filas

        if($selectedRule->predefined_zone) //Zona predefinida por usuario
            $positions = $selectedRule->block->positions->where('parent',$selectedRule->predefined_zone);
        
        else //Elige fila de entre las posibles
            $positions = $selectedRule->block->positions->where('type',2);            

        $opened = $positions->firstWhere('category', $selectedRule->name);    

        if($opened){ //Búsqueda de fila ya abierta de la regla a aplicar, en caso contrario buscar vacía más cercana
            $destinationLane = $opened;
        }
        else{
            $distanceRegister = Distance::where('origin',$vehicle->position->id)->min('seconds');
            $destinationLane = $distanceRegister->destination;
        }

        $destination = $destinationLane->reserve($vehicleInfo->design->length,$selectedRule->name);

    }

    return $destination;
}

function compare($vehSet,$rule){

    $ruleSet = array();

    foreach ($rule->definitions as $def) {

        $ruleSet[$def->relevant_id] = $def->value;
      
    }
    return count($vehSet) - count(array_diff($vehSet, $ruleSet));
}