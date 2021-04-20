<?php

namespace App\Model\helpdesk\Ticket;

use Illuminate\Database\Eloquent\Model;

class Halt extends Model
{
    // NOTE FROM AVINASH : halted_at column can be removed after v3.0.0, since same thing can be achieved by created_at
    // also, halted_at has some addition properties added in migration which makes it store timestamp in system timezone
    // instead of UTC
    protected $table = "halts";
    protected $fillable = ['ticket_id','halted_at','time_used','halted_time'];
    protected $dates = ['halted_at'];
}
