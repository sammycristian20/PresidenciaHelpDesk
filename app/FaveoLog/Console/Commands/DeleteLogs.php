<?php

namespace App\FaveoLog\Console\Commands;

use App\Console\LoggableCommand;
use Illuminate\Console\Command;
use App\Http\Controllers\Common\PhpMailController;
use Carbon\Carbon;
use Logger;
use Config;
use App\FaveoLog\controllers\LogViewController;
use Lang;

class DeleteLogs extends LoggableCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes system logs older than 7 days';

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
                $daysToKeepLogs = Config::get('app.log_max_days');

                $deleteBefore =  Carbon::now()->subDays($daysToKeepLogs);

                (new LogViewController)->deleteLogsByDate(['mail','cron','exception'], $deleteBefore);
        }
    }
}
