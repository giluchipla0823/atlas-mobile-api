<?php

namespace Database\Factories;


use App\Models\State;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $vin = strtoupper(Str::random(16));

        return [
            'vin' => $vin,
            'vin_short' => substr($vin, -6),
            'country_id' => 2,
            'design_id' => 2,
            'color_id' => 8,
            'position_id' => 2,
            'parent_compound' => 2,
            'state_id' => State::STATE_ID_ON_TERMINAL,
            'eoc' => '',
            'last_rule_id' => 7,
            'shipping_rule_id' => 7,
        ];
    }
}
