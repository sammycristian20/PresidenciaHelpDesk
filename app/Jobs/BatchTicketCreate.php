<?php

namespace App\Jobs;

use App\Facades\Attach;
use App\Helper\BatchTicketImport;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\User;
use App\Jobs\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use App\Model\MailJob\QueueService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\Common\FaveoMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Model\helpdesk\Utility\CountryCode;
use App\Http\Requests\helpdesk\Ticket\AgentPanelTicketRequest;
use App\Http\Controllers\Agent\helpdesk\UserController;
use App\Http\Controllers\Agent\helpdesk\TicketController;
use Illuminate\Http\UploadedFile;
use App\Traits\UserVerificationHelper;

class BatchTicketCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, UserVerificationHelper;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $data, $users_list = [], $file_name, $ticket, $code;

    public function __construct($data, $file_name)
    {
        $this->setDriver();
        $this->data = $data;
        $this->file_name = $file_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = FileSystemSettings::value('disk');
        \Excel::import(new BatchTicketImport($this->data), $this->file_name, $disk);
        Attach::delete($this->file_name, $disk);
    }

    private function setDriver(){
        $queue_driver = 'sync';
        if($driver = QueueService::where('status', 1)->first())
            $queue_driver = $driver->short_name;
        app('queue')->setDefaultDriver($queue_driver);
    }

    public function notifyUser(){
        $noti = \App\Model\helpdesk\Notification\Notification::create([
                    'message' => __('lang.batch-ticket-created-success'),
                    'to'      => \Auth::user()->id,
                    'by'      => \Auth::user()->id,
                    'table'   => "",
                    'row_id'  => "",
                    'url'     => "",
        ]);

        $content['content'] = "Hi ".\Auth::user()->user_name. ",<br> <br>".__('lang.batch-ticket-created-success');
        $content['subject'] = __('lang.Ticket-created-successfully');
        \Log::info($content);
        $faveoMail =  new FaveoMail;
        $faveoMail->sendMail(\Auth::user()->email,  $content, []);
    }
}
