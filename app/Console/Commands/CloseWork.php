<?php

namespace App\Console\Commands;

use App\Console\LoggableCommand;
use App\Http\Controllers\Client\helpdesk\UnAuthController;
use App\Http\Controllers\Common\PhpMailController;
use Lang;
use Carbon\Carbon;
use Logger;


class CloseWork extends LoggableCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes auto-close workflow on tickets';

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
           (new UnAuthController(new PhpMailController))->autoCloseTickets();
        }
    }
}
