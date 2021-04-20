<?php

namespace App\Events\Ticket;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketListenerEnforcing
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketValueArray;

    public $listenerId;

    /**
     * @param array $ticketValueArray Associated array of ticket values
     * @param $listenerId
     */
    public function __construct(array $ticketValueArray, $listenerId)
    {
        $this->ticketValueArray = $ticketValueArray;
        $this->listenerId = $listenerId;
    }
}
