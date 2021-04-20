<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        require base_path('routes/channels.php');

        /**
         * Requiring custom plugins channel
         */
        foreach (glob(app_path('Plugins/*'), GLOB_ONLYDIR) as $value) {
            $channels = $value.DIRECTORY_SEPARATOR."channels.php";
            if(file_exists($channels)) {
                require $channels;
            }
        }
    }
}