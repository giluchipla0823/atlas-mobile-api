<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompoundPages extends Model
{
    use HasFactory;
    
    protected $connection = 'config';
}
