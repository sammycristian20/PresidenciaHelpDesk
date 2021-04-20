<?php

namespace App\Plugins\Telephony\Console\Commands;

use App\Console\LoggableCommand;
use App\Plugins\Telephony\Model\TelephonyLog;
use App\Plugins\Telephony\Model\TelephonyProvider;
use App\Http\Controllers\Common\PhpMailController;
use App\Plugins\Telephony\Jobs\TicketConversionJob;

use \Carbon\Carbon;
/**
 * Artisan command to convert logged calls to ticket by dispatching the job
 *
 * @package App\Plugins\Telephony\Console\Commands
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 * @since v3.0.0
 */
class CallToTicketConversion extends LoggableCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telephony:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts pending logged calls into tickets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handleAndLog()
    {
        $providers = TelephonyProvider::where('conversion_waiting_time', '>', 0)->get(['conversion_waiting_time','short', 'id'])->toArray();
        $logDispatchedForConversion = [];
        foreach ($providers as $provider) {//iterate over each providers
            /**
             * Get First 5 call logs to convert them into tickets only if
             * - there is not ticket linked to the log
             * - if the log created before current time - conversion_waiting_time
             * - ticket creation job has not been dispatched already 
             */
            $logsToConvert = TelephonyLog::whereNull('call_ticket_id')->where([
                ['provider_id', '=', $provider['id']],
                ['created_at', '<', Carbon::now()->subMinutes($provider['conversion_waiting_time'])],
                ['job_dispatched', '=', 0]
            ])->take(5)->get();
            foreach ($logsToConvert as $callLog) {
                //dispatch ticket creation job for the log
                (new PhpMailController)->setQueue();
                dispatch(new TicketConversionJob($callLog));
                array_push($logDispatchedForConversion, $callLog->id);
            }
        }
        //set job_dispatched as 1 on the logs for which job has been dispatched
        TelephonyLog::whereIn('id', $logDispatchedForConversion)->update(['job_dispatched'=>1]);
    }
}
