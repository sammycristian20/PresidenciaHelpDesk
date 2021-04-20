<?php

namespace App\Model\helpdesk\Ticket;

use Illuminate\Database\Eloquent\Model;


class TicketActionEmail extends Model
{

  protected $table = 'ticket_action_emails';

  protected $fillable = ['subject', 'body', 'ticket_action_id'];

  public function users()
  {
      return $this->belongsToMany(\App\User::class);
  }
}
