<?php

namespace App\AutoAssign;

use App\Providers\ExtendServiceProvider as ServiceProvider;

class AutoAssignServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {

        $view_path = app_path() . DIRECTORY_SEPARATOR . 'AutoAssign' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'assign');

        $lang_path = app_path() . DIRECTORY_SEPARATOR . 'AutoAssign' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($lang_path, "assign");
        if (isInstall() == true) {
            $controller = new Controllers\ActivateController();
            $controller->activate();
        }

        if (class_exists('Breadcrumbs')){
            require __DIR__ . '/breadcrumbs.php';
        }   
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        // Add routes
        $routes = app_path('/AutoAssign/routes.php');
        if (file_exists($routes)) {
            $this->loadRoutesFrom($routes);
            require $routes;
        }
        $helper = app_path('/AutoAssign/helper.php');
        if (file_exists($helper)) {
            require $helper;
        }
    }

   

}
