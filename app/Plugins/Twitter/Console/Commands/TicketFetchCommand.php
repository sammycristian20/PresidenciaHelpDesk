<?php

namespace App\Plugins\Twitter\Console\Commands;

use App\Console\LoggableCommand;
use App\Plugins\Twitter\Handler\TwitterAPIHandler;
use Logger;

class TicketFetchCommand extends LoggableCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command fetches tickets from twitter.';

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
     * @throws \Throwable
     */
    public function handleAndLog()
    {
        (new TwitterAPIHandler())->fetchDataFromTwitter();
    }
}
