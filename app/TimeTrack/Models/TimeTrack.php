<?php

namespace App\TimeTrack\Models;

use Illuminate\Database\Eloquent\Model;

class TimeTrack extends Model
{
    protected $fillable = ['description', 'work_time'];

    protected function ticket()
    {
        return $this->belongsTo(\App\Model\helpdesk\Ticket\Tickets::class);
    }
}
