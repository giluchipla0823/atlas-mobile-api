<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Position;
use App\Models\Block;
use App\Models\Definition;
use App\Models\Carrier;

class Rule extends Model{
    use HasFactory;

    public function block(){

        return $this->belongsTo(Block::class);

    }

    public function carrier(){
        return $this->belongsTo(Carrier::class);
    }

    public function ruleInfo(){
        $this->definitions = $this->hasMany(Definition::class)->get();

        return $this;
    }

    public function directZone(){
        return $this->belongsTo(Position::class,'direct');
    }
}
