<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Position;
use App\Models\HoldBlock;
use App\Models\HoldDefiniton;


class Hold extends Model
{
    use HasFactory;

    public function positions(){
        return $this->hasManyThrough(Position::class, HoldBlock::class);
    }

    public function definitions()
    {
        return $this->hasMany(HoldDefiniton::class);
    }
}
