<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\FaveoReport\Models\ReportDownload;

class ReportExportEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $report;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ReportDownload $report)
    {
        $this->report = $report;
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
