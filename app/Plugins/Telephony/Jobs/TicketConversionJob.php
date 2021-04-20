<?php

namespace App\Plugins\Telephony\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use App\Plugins\Telephony\Controllers\CallHookHandler;

class TicketConversionJob implements ShouldQueue
{

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;
    public $tries = 5;
    
    public $callLog;

    public $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($callLog)
    {
        $this->callLog = $callLog;
        $this->request = new Request;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	(new CallHookHandler($this->request))->convertCallLogIntoTicket($this->callLog, $this->request, true);
    }
}
