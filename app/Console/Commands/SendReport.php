<?php

namespace App\Console\Commands;

use App\Console\LoggableCommand;
use App\Http\Controllers\Agent\helpdesk\NotificationController;
use App\Http\Controllers\Common\PhpMailController;
use Exception;

class SendReport extends LoggableCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends daily report as mail to agents';

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
            if (isInstall()) {
                $mail = new PhpMailController();
                $mail->setQueue();
                $this_report = new NotificationController($mail);
                $report = $this_report->send_notification();
            }
    }

}
