<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Page;

class Compound extends Model
{
    use HasFactory;

    protected $connection = 'auth';
    
    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}
