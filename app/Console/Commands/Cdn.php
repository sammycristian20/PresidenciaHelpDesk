<?php

namespace App\Console\Commands;

use App\Console\LoggableCommand;
use App\Http\Controllers\Admin\helpdesk\SettingsController;
use Logger;

class Cdn extends LoggableCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdn {--service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make CDN on or off';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handleAndLog()
    {
        $settingsController = new SettingsController();
        $service = $this->option('service');
        $service = $service == 'on' ? 1 : 0;
        $settingsController->cdnSettings($service);
        $this->info(trans('lang.cdn_service_updated_successfully'));
    }
}
