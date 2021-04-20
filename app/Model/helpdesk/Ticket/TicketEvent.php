<?php

namespace App\Model\helpdesk\Ticket;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;

class TicketEvent extends Model
{
    use Observable;

    protected $table = 'ticket_events';

    protected $fillable = ['field','from','to','ticket_listener_id'];

}
