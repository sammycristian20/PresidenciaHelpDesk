<?php

namespace App\MicroOrganization;

use App\Providers\ExtendServiceProvider as ServiceProvider;

class MicroOrganizationServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {

        $view_path = app_path() . DIRECTORY_SEPARATOR . 'MicroOrganization' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'MicroOrganization');

        // $lang_path = app_path() . DIRECTORY_SEPARATOR . 'MicroOrganization' . DIRECTORY_SEPARATOR . 'lang';
        // $this->loadTranslationsFrom($lang_path, "MicroOrganization");
        // if (isInstall()) {
        //     $controller = new Controllers\ActivateController();
        //     $controller->activate();
        // }

        if (class_exists('Breadcrumbs')) {
            require __DIR__ . DIRECTORY_SEPARATOR . 'breadcrumbs.php';
        }    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        // Add routes
        $routes = app_path('/MicroOrganization/routes.php');
        // dd($routes);
        if (file_exists($routes)) {
            require $routes;
        }
    }

}
