<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tile;

class Page extends Model
{
    use HasFactory;

    protected $connection = 'config';

    public function tiles(){
        return $this->hasMany(Tile::class)->where('active',1)->orderBy('action');
    }
}
