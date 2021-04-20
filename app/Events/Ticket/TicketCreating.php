<?php

namespace App\Events\Ticket;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCreating
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketValueArray;

    /**
     * @param array $ticketValueArray Associated array of ticket values
     */
    public function __construct(array $ticketValueArray)
    {
        $this->ticketValueArray = $ticketValueArray;
    }
}
