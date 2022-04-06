<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $connection = 'auth';

    protected $fillable = [
        'name',
        'uuid',
        'type_id',
        // 'version'
    ];
}
