<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tile extends Model
{
    use HasFactory;

    protected $connection = 'config';

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'page_id',
        'active'
    ];
}
