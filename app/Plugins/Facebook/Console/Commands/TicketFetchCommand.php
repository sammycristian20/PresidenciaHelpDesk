<?php

namespace App\Plugins\Facebook\Console\Commands;

use App\Console\LoggableCommand;
use App\Plugins\Facebook\Cron\FacebookCronProcessor;
use Logger;

class TicketFetchCommand extends LoggableCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command fetches tickets from facebook page inbox.';

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
     * @return mixed
     */
    public function handleAndLog()
    {
        (new FacebookCronProcessor())->processFacebookMessages();
    }
}
