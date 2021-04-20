<?php


namespace App\Plugins\Calendar\Jobs;


use App\Plugins\Calendar\Handler\TaskNotificationHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TaskNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notifier;

    public function __construct(TaskNotificationHandler $notifier)
    {
       $this->notifier = $notifier;
    }

    public function handle()
    {
        $this->notifier->handle();
    }

}
