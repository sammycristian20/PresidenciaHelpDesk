<?php

namespace App\Plugins\Calendar\Jobs;

use App\Http\Controllers\Common\PhpMailController;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Settings\Email as SystemMail;
use App\Plugins\Calendar\Model\TaskMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TaskNotificationProcessorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $pendingMails = TaskMail::where('processed', 0)->get();

        $from = Emails::find(SystemMail::pluck('sys_email')->first());

        if ($from) {
            foreach ($pendingMails as $pendingMail) {
                $toAddress = [
                    'name' => "",
                    'email' => $pendingMail->to
                ];

                $message = [
                    'subject' => $pendingMail->subject,
                    'scenario' => null,
                    'body' => $pendingMail->content
                ];

                (new PhpMailController)->sendmail($from->id, $toAddress, $message,[],[]);

                $pendingMail->update(['processed' => 1]);
            }
        }
    }
}