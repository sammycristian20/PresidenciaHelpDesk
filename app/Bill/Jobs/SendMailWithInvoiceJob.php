<?php

namespace App\Bill\Jobs;

use App\Http\Controllers\Common\PhpMailController;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Bill\Controllers\InvoiceController;
use Lang;
use Logger;

class SendMailWithInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 180;

    protected $alert;

    protected $to;

    protected $scenario;

    protected $invioceId;

    protected $subject;

    protected $variable;

    protected $directSend;

    protected $userId;
    /**
     * Create a new job instance.
     * 1, 1
     * @return void
     */
    public function __construct(String $alert, Array $to, String $scenario, int $invioceId, String $subject =null, $variable = [], $directSend = false, $userId=null)
    {
        $this->alert      = $alert;
        $this->to         = $to;
        $this->scenario   = $scenario; 
        $this->invioceId  = $invioceId;
        $this->subject    = $subject;
        $this->variable   = $variable;
        $this->directSend = $directSend;
        $this->userId     = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void|bool
     */
    public function handle()
    {
        try {
            $controller = new InvoiceController;
            $attachments = $controller->getInvoiceAsAttachment($this->invioceId);
            $controller->handleMail($this->alert, $this->to, $this->scenario, $this->subject, [$attachments], $controller->getInvoiceMailVariables($this->invioceId), $this->directSend, $this->userId);
        } catch(\Exception $e) {
            Logger::exception($e);
        }
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {

    }
}
