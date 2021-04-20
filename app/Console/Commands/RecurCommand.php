<?php

namespace App\Console\Commands;

use App\Console\LoggableCommand;
use Lang;
use App\Http\Controllers\Admin\helpdesk\TicketRecurController;
use Logger;
use Carbon\Carbon;


class RecurCommand extends LoggableCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:recur';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute ticket recurring command';

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
                (new ticketRecurController)->recur();
        }
    }

}
