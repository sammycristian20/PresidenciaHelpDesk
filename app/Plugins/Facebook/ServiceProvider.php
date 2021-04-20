<?php

namespace App\Plugins\Facebook;

use App\Plugins\Facebook\Console\Commands\TicketFetchCommand;
use App\Plugins\SyncPluginToLatestVersion;
use Artisan;
use Illuminate\Support\Facades\Schema;

class ServiceProvider extends \App\Plugins\ServiceProvider
{

    public function register()
    {

        parent::register('Facebook');
        $this->registerMiddlewareOfPackage([
           'facebook' => 'App\Plugins\Facebook\Middleware\VerifyRequestFromFacebook'
        ]);
    }

    public function boot()
    {

        (new SyncPluginToLatestVersion)->sync('Facebook');

        if ($this->app->runningInConsole()) {
            $this->commands([TicketFetchCommand::class]);
        }

        /**
         * Views
         */
        $view_path = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Facebook' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'facebook');

        if (class_exists('Breadcrumbs')) {
            require __DIR__ . '/breadcrumbs.php';
        }

        /**
         * Translation
         */
        $trans = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Facebook' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($trans, 'Facebook');

        parent::boot('Facebook');
    }
}
