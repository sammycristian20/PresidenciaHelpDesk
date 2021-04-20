<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CustomOutboundEmail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $to_email;
    public $to_name;
    public $subject;
    public $data;
    public $cc;
    public $attach;
    public $thread;
    public $auto_respond;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($event)
    {
        $this->to_email = $event['to_email'];
        $this->to_name = $event['to_name'];
        $this->subject = $event['subject'];
        $this->data = $event['data'];
        $this->cc = $event['cc'];
        $this->attach = $event['attach'];
        $this->thread = $event['thread'];
        $this->auto_respond = $event['auto_respond'];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
