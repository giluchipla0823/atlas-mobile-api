<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Position;

class Block extends Model
{
    use HasFactory;

    public function positions(){
        return $this->hasMany(Position::class);
    }
}
