<?php

namespace App\Events\Ticket;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketThreadCreating
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $threadValueArray;

    /**
     * @param array $threadValueArray
     */
    public function __construct(array $threadValueArray)
    {
        $this->threadValueArray = $threadValueArray;
    }
}
