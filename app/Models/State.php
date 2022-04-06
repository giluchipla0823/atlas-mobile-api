<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    public const STATE_ID_ON_TERMINAL = 1;
    public const STATE_ID_DRIVEN = 2;
    public const STATE_ID_STATIONED = 3;
    public const STATE_ID_READY = 5;
    public const STATE_ID_HOLD = 7;
}
