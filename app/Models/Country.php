<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle;
use App\Models\DestinationCode;

class Country extends Model
{
    use HasFactory;

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class,'country');
    }

    public function destinationCodes()
    {
        return $this->hasMany(DestinationCode::class);
    }
}
