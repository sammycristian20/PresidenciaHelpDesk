<?php

namespace App\Events\Ticket;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketForwarding
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketId;

    public $forwardedTo;

    /**
     * @param array $ticketValueArray Associated array of ticket values
     */
    public function __construct(int $ticketId, array $forwardedTo)
    {
        // need id and forwarded_to as array of user-ids
        $this->ticketId = $ticketId;
        $this->forwardedTo = $forwardedTo;
    }
}
