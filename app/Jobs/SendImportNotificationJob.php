<?php

namespace App\Jobs;

use App\Http\Controllers\Common\NotificationController;
use App\Model\helpdesk\Notification\PushNotification;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/*
 * Sends in app notification after import is successfully completed
 * NOTE: This class is created cause App/Jobs/Notifications is inconsistent
 * This may be deleted after App/Jobs/Notification class is consistent
 */
class SendImportNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @param NotificationController $notify
     * @return void
     */
    public function handle(NotificationController $notify)
    {
        $notify->createNotification($this->data);
        $this->inAppWebPush();
    }

    protected function inAppWebPush()
    {
        $recepients = array_unique(explode(',', $this->data['to']));

        $message = str_replace(['<b>','</b>'], ['',''], $this->data['message']);

        $message = (is_numeric($this->data['by'])) ?
            User::where('id', $this->data['by'])->first()->user_name . " " . $message
            : "System ".$message;

        foreach ($recepients as $recepient) {
            PushNotification::create([
                'message' => $message,
                'url' => $this->data['url'],
                'subscribed_user_id' => $recepient,
                'status' => 'pending'
            ]);
        }
    }
}
