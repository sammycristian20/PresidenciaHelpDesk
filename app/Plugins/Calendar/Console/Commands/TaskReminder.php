<?php

namespace App\Plugins\Calendar\Console\Commands;

use App\Console\LoggableCommand;
use App\Plugins\Calendar\Controllers\TaskController;

class TaskReminder extends LoggableCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Repeated Alert For Tasks.';

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
     * @return void
     */
    public function handleAndLog()
    {
        (new TaskController)->sendReminders();
    }
}
