<?php

namespace App\Plugins\Telephony\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncomingCallEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $to;

    protected $from;

    protected $broadCastAs;

    protected $callId;

    protected $callFrom;

    protected $conversionWaitingTime;

    protected $allowedConversion;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($to, $from , $broadCastAs, $callId, $callFrom, $conversionWaitingTime=0, $allowedConversion)
    {
        $this->to = $to;
        $this->from = $from;
        $this->broadCastAs = $broadCastAs;
        $this->callId = $callId;
        $this->callFrom = $callFrom;
        $this->conversionWaitingTime = ($conversionWaitingTime*60)?:120;
        $this->allowedConversion = $allowedConversion;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user-notifications.'.$this->to);
    }

    public function broadcastAs()
    {
    	return $this->broadCastAs;
    }

    public function broadcastWith()
    {
        return [
        	'is_registered_user' => (bool)$this->from['name'],
        	'user' => $this->from,
            'call_id' => $this->callId,
            'call_from' => $this->callFrom,
            'conversion_waiting_time'=> $this->conversionWaitingTime-15,
            'allow_ticket_conversion' => $this->allowedConversion
        ];
    }
}
