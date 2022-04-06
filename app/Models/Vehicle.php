<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vin',
        'vin_short',
        'country_id',
        'destination_code_id',
        'design_id',
        'color_id',
        'position_id',
        'parent_compound',
        'state_id',
        'eoc',
        'last_rule_id',
        'shipping_rule_id',
        'route_to',
        'dt_onterminal',
        'dt_left',
        'load_id',
        'on_route',
    ];

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function holds(){
        return $this->belongsToMany(Hold::class);
    }

    public function category()
    {
        return $this->belongsTo(Rule::class,'shipping_rule_id');
    }

    /**
     * Obtener información más detallada del vehículo.
     *
     * @return object
     */
    public function info (): object
    {
        return (object) [
            'vin' => $this->vin,
            'vinShort' => $this->vin_short,
            'color' => $this->color()->first(),
            'country' => $this->country()->first(),
            'design' => $this->design()->first(),
            'position' => $this->position()->first(),
            'state' => $this->state()->first(),
            'category' => $this->category()->first(),
        ];
    }

    /**
     * TODO: Revisar esta funcionalidad
     * Receive Vehicle.
     *
     * @param int $compoundId
     * @param int|null $type
     * @return bool
     */
    public function receive(int $compoundId, ?int $type): bool
    {
        $this->state_id =  State::STATE_ID_ON_TERMINAL;

        $parameter = CompoundConfig::PARAMETER_POSITION_DEFAULT;

        switch ($type) {
            case 1:
                $parameter = CompoundConfig::PARAMETER_POSITION_TRUCK;
                break;
            case 2:
                $parameter = CompoundConfig::PARAMETER_POSITION_FACTORY;
                break;
            case 3:
                $parameter = CompoundConfig::PARAMETER_POSITION_SHIP;
                break;
            case 4:
                $parameter = CompoundConfig::PARAMETER_POSITION_TRAIN;
                break;
        }

        $this->position_id = Position::find($compoundId)->singleConfig($parameter);

        return $this->save();
    }

}
