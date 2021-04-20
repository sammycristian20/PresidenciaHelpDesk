<?php

namespace App\Console\Commands;

use App\Console\LoggableCommand;
use App\Http\Controllers\SLA\Reminders;
use Carbon\Carbon;
use Logger;
use Lang;

class SendEscalate extends LoggableCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:escalation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the escalation notifications for escalated tickets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
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
                (new Reminders())->sendReminders();
        }
    }
}
