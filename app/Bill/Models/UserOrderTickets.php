<?php

namespace App\Bill\Models;

use App\BaseModel;

class UserOrderTickets extends BaseModel
{
    protected $table = 'user_order_tickets';
    
    protected $fillable = ['id', 'ticket_id', 'user_id', 'order_id','created_at','updated_at'];



    public function ticket()
    {
     	return $this->belongsTo('App\Model\helpdesk\Ticket\Tickets', 'ticket_id');
    }
}
