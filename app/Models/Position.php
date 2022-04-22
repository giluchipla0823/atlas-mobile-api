<?php

namespace App\Models;

use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Symfony\Component\HttpFoundation\Response;


class Position extends Model
{
    use HasFactory;

    public const TYPE_COMPOUND = 0;
    public const TYPE_PARKING = 1;
    public const TYPE_ROW = 2;
    public const TYPE_SLOT = 3;

    public const ROW_TYPE_NORMAL = 0;
    public const ROW_TYPE_WITHOUT_ROWS = 1;
    public const ROW_TYPE_ESPIGA = 2;

    public const DIRECT_POSITION_ON = 1;
    public const DIRECT_POSITION_OFF = 0;


    public $timestamps = false;

    /**
     * @return HasOne
     */
    public function vehicle(): HasOne
    {
        return $this->hasOne(Vehicle::class);
    }

    public function children() {
        return $this->hasMany(Position::class,'parent');
    }

    public function parent() {
        return $this->belongsTo(Position::class,'parent');
    }

    public function nextPos() {
        return $this->hasOne(Position::class,'prev');
    }

    public function prevPos() {
        return $this->hasOne(Position::class,'next');
    }

    public function config() {
        return $this->hasMany(CompoundConfig::class,'compound_id');
    }

    public function singleConfig($param) {
        $model = $this->hasMany(CompoundConfig::class,'compound_id')
                ->where('parameter', $param)
                ->first();

        return $model ? $model->value : null;
    }


    /**
     *
     * @return array
     */
    public function fishboneAroundPosition(): array {
        $nextPosition = $this->nextPos()->first(); // Siguiente
        $prevPosition = $this->prevPos()->first(); // Anterior

        // dd($this);
        // dd($nextPosition, $prevPosition);

        $nextFirstVehicle = null;
        $nextSecondVehicle = null;

        if ($nextPosition) {
            $nextFirstVehicle = $nextPosition->vehicle ? $nextPosition->vehicle->info() : null;
            $position = $nextPosition->nextPos()->first();

            if ($position && $position->vehicle) {
                $nextSecondVehicle = $position->vehicle->info();
            }
        }

        $prevFirstVehicle = null;
        $prevSecondVehicle = null;

        if ($prevPosition) {
            $prevFirstVehicle = $prevPosition->vehicle ? $prevPosition->vehicle->info() : null;
            $position = $prevPosition->prevPos()->first();

            if ($position && $position->vehicle) {
                $prevSecondVehicle = $position->vehicle->info();
            }
        }

        $vehicles = [$prevSecondVehicle, $prevFirstVehicle, $nextFirstVehicle, $nextSecondVehicle];

        $positions = [];

        foreach ($vehicles as $vehicle) {
            $positions[] = [
                'exists' => !is_null($vehicle),
                'current' => false,
                'vehicle' => $vehicle ?: ['color' => ['hex' => '#d3d3d3']]
            ];
        }

        $current = [['exists' => true, 'current' => true]];

        array_splice($positions, 2, 0, $current);

        return $positions;
    }

    /**
     * Obtener el slot disponible de una fila.
     *
     * @return Position|null
     * @throws Exception
     */
    public function slotAvailable(): ?Position
    {
        if ($this->type !== self::TYPE_ROW) {
            throw new Exception(
                "Para obtener el slot disponible, la posición seleccionada debe ser una fila.",
                Response::HTTP_BAD_REQUEST
            );
        }

        // Obtener las slots de la fila.
        $slots = $this->children()->get();

        // dd($slots);

        if (count($slots) === 0) {
            throw new Exception("La fila seleccionada no tiene slots asignados.", Response::HTTP_BAD_REQUEST);
        }

        return $slots->filter(function (Position $slot) {
            return $slot->fill === 0;
        })->first();
    }

    /**
     * Reservar espacio en slot, fila, parking y campa. Devuelve el slot disponible.
     * Para realizar esta funcionalidad, la posición de la cual se hace la llamada esta
     * función debe ser tipo "fila".
     *
     * @param int $length
     * @param string $category
     * @return Position|null
     * @throws Exception
     */
    public function reserve(int $length, string $category): ?Position
    {
//        // Obtener las slots de la fila.
//        $current = $this->children()->get()->filter(function ($slot, $key) {
//            return !$slot->prev;
//        })->first();
//
//        // dd($current);
//
//        if (!$current) {
//            return null;
//        }
//
//        while($current->fill != 0){
//            if ($current->nextPos) {
//                $current = $current->nextPos;
//            }
//            else{
//                $current = null;
//                break;
//            }
//        }
//
//        if (!$current) {
//            return null;
//        }

        /*
        if (!$current = $this->slotAvailable()) {
            return null;
        }
        */

        // Slot disponible
        $current = $this->slotAvailable();

        // Reserva slot disponible
        $current->category = $category;
        $current->increment('fill');
        $current->increment('fillmm',$length);
        $current->save();

        // Reserva fila
        $this->increment('fill');
        $this->increment('fillmm',$length);
        $this->category = $category;
        $this->save();

        // Reserva parking
        $parking = $this->parent()->first();
        $parking->increment('fill');

        // Reserva compound
        $compound = $parking->parent()->first();
        $compound->increment('fill');

        return $current;
    }

    /**
     * Ocupar espacio.
     *
     * @param int $length
     * @return void
     */
    public function ocupar(int $length) {
        $compound = null;

        switch (intval($this->type)) {
            case self::TYPE_PARKING:
                $this->increment('fill');
                $this->increment('fillmm', $length);

                $compound = Position::find($this->parent);
                break;

            case self::TYPE_SLOT:

                // Ocupar espacio en Slot
                $this->fill = 1;
                $this->fillmm = $length;
                $this->save();

                // Ocupar espacio en Fila
                $row = Position::find($this->parent);
                $row->increment('fill');
                $row->increment('fillmm', $length);

                // Ocupar espacio en Parking
                if ($parking = Position::find($row->parent)) {
                    $parking->increment('fill');
                    $parking->increment('fillmm', $length);
                }

                $compound = Position::find($parking->parent);
                break;

            default:
                break;
        }

        if ($compound) {
            $compound->increment('fill');
            $compound->increment('fillmm', $length);
        }
    }

    /**
     * Liberar espacio.
     *
     * @param int $length
     * @return void
     */
    public function liberar(int $length) {

        switch (intval($this->type)) {
            case self::TYPE_PARKING:
                // Liberar espacio parking
                $this->decrement('fill');
                $this->decrement('fillmm', $length);

                // Liberar espacio campa.
                if ($compound = Position::find($this->parent)) {
                    $compound->decrement('fill');
                    $compound->decrement('fillmm', $length);
                }
                break;

            case self::TYPE_SLOT:

                // Liberar Slot
                // $this->category = null;
                $this->fill = 0;
                $this->fillmm = 0;
                $this->save();

                // Liberar espacio en Row
                $row = Position::find($this->parent);
                $row->decrement('fill');
                $row->decrement('fillmm', $length);

                // Liberar espacio en Parking
                if ($parking = Position::find($row->parent)) {
                    $parking->decrement('fill');
                    $parking->decrement('fillmm', $length);
                }

                if ($compound = Position::find($parking->parent)) {
                    $compound->decrement('fill');
                    $compound->decrement('fillmm', $length);
                }

                break;

            default:
                break;
        }
    }



    /**
     * Liberar espacio.
     *
     * @param int $length
     * @return void
     */
    public function release(int $length) {

        switch (intval($this->type)) {
            case self::TYPE_PARKING:
                $compound = Position::find($this->parent);
                $compound->decrement('fill');
                $compound->decrement('fillmm', $length);
                break;

            case self::TYPE_SLOT:

                // Liberar Slot
                // $this->category = null;
                $this->fill = 0;
                $this->fillmm = 0;
                $this->save();

                // Liberar espacio en Row
                $row = Position::find($this->parent);
                $row->decrement('fill');
                $row->decrement('fillmm', $length);

//                if((int) $row->fill === 1){
//                    $row->category = null;
//                    $row->save();
//                }

                // Liberar espacio en Parking
                if ($parking = Position::find($row->parent)) {
                    $parking->decrement('fill');
                    $parking->decrement('fillmm', $length);
                }

                if ($compound = Position::find($parking->parent)) {
                    $compound->decrement('fill');
                    $compound->decrement('fillmm', $length);
                }

                break;

            default:
                break;
        }
    }

    /**
     * @param Vehicle $vehicle
     * @param Rule $category
     * @param int $userId
     * @param DateTime $dtOrigin
     * @return $this
     */
    public function placeVehicle(Vehicle $vehicle, Rule $category, int $userId, DateTime $dtOrigin): Position
    {
        $vehicleInfo = $vehicle->info();

        if ($this->type == self::TYPE_SLOT) {
            $this->fill = 1;
            $this->fillmm = $vehicleInfo->design->length;
            $this->category = $vehicleInfo->category->name;
            $this->save();

            $row = Position::find($this->parent);
            $row->increment('fill');
            $row->increment('fillmm',$vehicle->design->length);
            $row->category = $vehicleInfo->category->name;
            $row->save();

            $parking = Position::find($row->parent);
            $parking->increment('fill');

            $compound = Position::find($parking->parent);
        }
        else{
            $this->increment('fill');

            $compound = Position::find($this->parent);
        }

        if ($compound) {
            $compound->increment('fill');
        }

        /**
         * DUDA ¿Para qué se hace esto?. si el movimiento al final se queda como una instancia, pero nunca se guarda.
         */
//        $movement = new Movement();
//        $movement->parent_compound = $this->parent_compound;
//        $movement->vehicle_id = $vehicle->id;
//        $movement->user_id = $userId;
//        $movement->origin_id = $vehicle->position_id;
//        $movement->rule_id = $category->id;
//        $movement->destination_id = $positionId;
//        $movement->dt_start = Carbon::parse($dtOrigin);
//        $movement->dt_end = Carbon::now();
//        $movement->save();

        return $this;
    }

    /**
     * Guardar movimiento.
     * Incrementar valor del campo "fill" del parking, fila y campa.
     * Incrementar valor del campo "fillmm" de la fila.
     *
     * @param Vehicle $vehicle
     * @param int $originId
     * @param int $destinationId
     * @param Rule $category
     * @param int $userId
     * @param DateTime $dtOrigin
     * @return $this
     */
    public function placeVehicle2(
        Vehicle $vehicle,
        int $originId,
        int $destinationId,
        Rule $category,
        int $userId,
        DateTime $dtOrigin
    ): Position
    {
        $vehicleInfo = $vehicle->info();
        $length = $vehicleInfo->design->length;
        $categoryName = $vehicleInfo->category->name;

        switch ($this->type) {
            case self::TYPE_SLOT:
                // Actualizar en slot
                $this->fill = 1;
                $this->fillmm = $length;
                $this->category = $categoryName;
                $this->save();

                // Actualizar en fila.
                $row = Position::find($this->parent);
                $row->increment('fill');
                $row->increment('fillmm', $length);
                $row->category = $vehicleInfo->category->name;
                $row->save();

                // Actualizar en parking.
                $parking = Position::find($row->parent);
                $parking->increment('fill');
                $parking->increment('fillmm', $length);

                $compound = Position::find($parking->parent);
                break;

            case self::TYPE_PARKING:
                $this->increment('fill');
                $this->increment('fillmm', $vehicle->design->length);

                $compound = Position::find($this->parent);
                break;

            default:
                $compound = Position::find($this->parent);
                break;
        }

        if ($compound) {
            // Actualizar en campa
            $compound->increment('fill');
            $compound->increment('fillmm', $length);
        }

        // Grabar movimiento
        $movement = new Movement();
        $movement->parent_compound = $this->parent_compound;
        $movement->vehicle_id = $vehicle->id;
        $movement->user_id = $userId;
        $movement->origin_id = $originId;
        $movement->destination_id = $destinationId;
        $movement->rule_id = $category->id;
        $movement->dt_start = Carbon::parse($dtOrigin);
        $movement->dt_end = Carbon::now();
        $movement->save();

        return $this;
    }

    public function toggleActive(){
        $this->timestamps = true;
        $this->active = !$this->active;
        return $this->save();
    }


    /**
     * @return array
     */
    public function createLoad(): array {

        $load =  new Load;
        $rule = Rule::where('name',$this->category)->first();

        // $load->carrier_id = $rule->carrierId;
        $load->carrier_id = $rule->carrier_id; // DUDA ¿Este campo no estaba en base de datos, porque se agregó?
        $load->rule_id = $rule->id;
        $load->parent_compound = $rule->parent_compound; // ¿Que debo guardar aquí?
        $load->code = 'xx'; // DUDA ¿?Qué debo guardar aquí?
        $load->save();

        $vehicles = $this->children->map(function($slot){
            return $slot->vehicle;
        });

        foreach ($vehicles as $vehicle){

            /**
             * Esto se hace porque puede venir en el array de "vehicles" un valor "null"
             */
            if ($vehicle) {
                $vehicle->load_id = $load->id;
                $vehicle->save();
            }
        }

        return ['error' => false, 'data' => $load];
    }
}
