<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    public function emit($name,$code,$action,$user,$parent_compound, $reference_id){

        $dt = Carbon::now();

        $this->name = $name;
        $this->code = $code;
        $this->action = $action;
        $this->user = $user;
        $this->parent_compound = $parent_compound;
        $this->dt = $dt;
        $this->reference_id =$reference_id;


        $this->save();
    }
}
