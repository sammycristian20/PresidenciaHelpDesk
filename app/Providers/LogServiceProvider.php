<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('Log', function(){
          return new \App\FaveoLog\controllers\LogWriteController;
        });
    }
}
