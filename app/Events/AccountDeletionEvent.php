<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\User;

/**
 * Event gets dispatched during deleting the user accounts so different
 * modules and packages can listen for the deactivation event and take necessary
 * actions for their modules.
 *
 * @package App\Events
 * @since v4.0.0
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 */
class AccountDeletionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User account for deletion
     */
    public $user;

    /**
     * @var string account deletion event type: requesting/processing
     */
    public $event;

    /**
     * @var User who is performing the action.
     */
    public $actor;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, User $actor, string $event)
    {
        $this->user = $user;
        $this->event = $event;
        $this->actor = $actor;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //
    }
}
