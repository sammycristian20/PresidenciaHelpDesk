<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use App\Model\MailJob\QueueService;
use App\Http\Controllers\Agent\helpdesk\DeleteAccountController;
/**
 * Job for processing account deletion.
 * @package App\Jobs
 * @since v4.0.0
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 */
class DeleteAccountJob implements ShouldQueue
{

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;
    public $tries = 5;

    /**
     * @var User for deletion
     */
    public $user;

    /**
     * @var User who is performing the action
     */
    public $actor;

    /**
     * @var array containing request data
     */
    public $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, User $actor, array $request)
    {
        $this->user = $user;
        $this->request = $request;
        $this->actor = $actor;
        $this->setDriver();
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            (new DeleteAccountController)->processDeleteJob($this->user, $this->actor, $this->request);
        } catch(\Exception $e) {
            /**
             * while processing the job if error occurs then we set processing_account_disabling to
             * 0 so the action can be taken again.
             */
            $this->user->processing_account_disabling = 0;  
            $this->user->save();          
        }
    }
    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
       \Logger::exception($exception);
    }

    private function setDriver()
    {
        $queueDriver = 'sync';
        if($driver = QueueService::where('status', 1)->first())
            $queueDriver = $driver->short_name;
        app('queue')->setDefaultDriver($queueDriver);
    }
}
