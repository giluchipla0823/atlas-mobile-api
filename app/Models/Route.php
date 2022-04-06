<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DestinationCode;

class Route extends Model
{
    use HasFactory;

    public function destinationCode()
    {
        return $this->belongsTo(DestinationCode::class);
    }
}
