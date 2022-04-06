<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Route;

class DestinationCode extends Model
{
    use HasFactory;

    public function routes()
    {
        return $this->hasMany(Route::class);
    }   
}
