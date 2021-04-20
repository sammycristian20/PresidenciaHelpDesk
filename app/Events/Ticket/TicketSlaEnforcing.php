<?php

namespace App\Events\Ticket;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketSlaEnforcing
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketValueArray;

    public $slaId;

    /**
     * @param array $ticketValueArray Associated array of ticket values
     * @param $workflowId
     */
    public function __construct(array $ticketValueArray, $slaId)
    {
        $this->ticketValueArray = $ticketValueArray;
        $this->slaId = $slaId;
    }
}
