<?php

namespace App\Console\Commands;

use Updater;
use GuzzleHttp\Client;
use App\Console\LoggableCommand;
use App\Http\Controllers\Update\AutoUpdateController;

class CheckUpdate extends LoggableCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'faveo:checkupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for latest system updates';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handleAndLog()
    {
        $cont = new AutoUpdateController(new Updater(),new Client());
        $cont->getLatestRelease();
    }
}
