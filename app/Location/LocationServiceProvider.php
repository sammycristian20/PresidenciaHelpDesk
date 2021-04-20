<?php

namespace App\Location;

use App\Providers\ExtendServiceProvider as ServiceProvider;

class LocationServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {

        $view_path = app_path() . DIRECTORY_SEPARATOR . 'Location' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'location');

        $lang_path = app_path() . DIRECTORY_SEPARATOR . 'Location' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($lang_path, "location");
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
    public function register() {
        // Add routes
        $routes = app_path('/Location/routes.php');
        //dd();
        if (file_exists($routes)) {
            require $routes;
        }
    }

}
