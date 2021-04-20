<?php

namespace App\FaveoReport;

use App\Providers\ExtendServiceProvider as ServiceProvider;

class ReportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations');

        $this->loadViewsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'views', 'report');

        $this->loadTranslationsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'lang', "report");

        if (class_exists('Breadcrumbs')) {
            require __DIR__ . DIRECTORY_SEPARATOR . 'breadcrumbs.php';
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->loadRoutesFrom(__DIR__ . DIRECTORY_SEPARATOR . 'routes.php');

        $helper = __DIR__ . DIRECTORY_SEPARATOR . 'helper.php';

        if (file_exists($helper)) {
            require $helper;
        }
    }
}
