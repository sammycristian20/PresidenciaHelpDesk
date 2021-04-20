<?php

namespace App\Model\Listener;

use Illuminate\Database\Eloquent\Model;

class ListenerEvent extends Model
{
    protected $table = 'listener_events';
    protected $fillable = ['listener_id','event','condition','old','new'];
}
