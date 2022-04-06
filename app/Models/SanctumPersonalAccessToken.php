<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken;

class SanctumPersonalAccessToken extends PersonalAccessToken
{
    protected $connection = 'auth';
    protected $table = 'personal_access_tokens';
}
