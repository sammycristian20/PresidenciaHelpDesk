<?php

namespace App\TimeTrack;

use App\Providers\ExtendServiceProvider as ServiceProvider;

class TimeTrackServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $view_path = app_path() . DIRECTORY_SEPARATOR . 'TimeTrack' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'timetrack');

        $lang_path = app_path() . DIRECTORY_SEPARATOR . 'TimeTrack' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($lang_path, "timetrack");

        if (isInstall()) {
            $controller = new Controllers\ActivateController();
            $controller->activate();
        }

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
        // Add routes
        $routes = app_path('/TimeTrack/routes.php');
        
        if (file_exists($routes)) {
            require $routes;
        }
    }

}
