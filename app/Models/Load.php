<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Load extends Model
{
    use HasFactory;

    public function notify($vinNum,$user,$compoundId){

        $notification = new Notification;

        $notification->emit($this->code,'load-loaded',"$vinNum vehicles have been added to load $this->code",$user,$compoundId, null);

    }
}
