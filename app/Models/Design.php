<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle;
use App\Models\Brand;
use App\Models\Color;

class Design extends Model
{
    use HasFactory;

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function colors()
    {
        return $this->hasMany(Color::class);
    }
}
