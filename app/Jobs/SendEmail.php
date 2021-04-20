<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\Common\PhpMailController;
use Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    public $tries = 5;
    protected $recipients;
    protected $recipientname;
    protected $subject;
    protected $content;
    protected $fromAddress;
    protected $ccMails;
    protected $attachment;
    protected $thread;
    protected $autoRespond;
    protected $logIdentifier;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipients, $recipientname, $subject, $content, $fromAddress,$ccMails, $attachment, $thread, $autoRespond, $logIdentifier)
    {
        $this->recipients = $recipients;
        $this->recipientname = $recipientname;
        $this->subject = $subject;
        $this->content = $content;
        $this->fromAddress = $fromAddress;
        $this->ccMails = $ccMails;
        $this->attachment = $attachment;
        $this->thread = $thread;
        $this->autoRespond = $autoRespond;
        $this->logIdentifier = $logIdentifier;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PhpMailController $PhpMailController){
         $p = $PhpMailController->laravelMail(
           $this->recipients,
           $this->recipientname,
           $this->subject,
           $this->content,
           $this->fromAddress,
           $this->ccMails,
           $this->attachment,
           $this->thread,
           $this->autoRespond,
           $this->logIdentifier
         );
         return $p;
    }


}
