<?php

namespace App\Models;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distance extends Model
{
    use HasFactory;

    public function destination() {
        return $this->belongsTo(Position::class,'destination_id');
    }

    public function origin() {
        return $this->belongsTo(Position::class,'origin_id');
    }
}
