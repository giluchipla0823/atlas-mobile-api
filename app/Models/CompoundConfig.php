<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompoundConfig extends Model
{
    use HasFactory;

    public const PARAMETER_POSITION_PLANT = 'initial_position_plant';
    public const PARAMETER_POSITION_TRUCK = 'initial_position_truck';
    public const PARAMETER_POSITION_FACTORY = 'initial_position_factory';
    public const PARAMETER_POSITION_SHIP = 'initial_position_ship';
    public const PARAMETER_POSITION_TRAIN = 'initial_position_train';
    public const PARAMETER_POSITION_DEFAULT = 'initial_position_default';
    public const PARAMETER_DEFAULT_OVERFLOW = 'default_overflow';
    public const PARAMETER_POSITION_BUFFER = 'initial_position_buffer';

    protected $connection = 'config';
}
