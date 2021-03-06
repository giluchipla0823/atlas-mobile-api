<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle;

class Brand extends Model
{
    use HasFactory;

    public function designs()
    {
        return $this->hasMany(Design::class);
    }
}
