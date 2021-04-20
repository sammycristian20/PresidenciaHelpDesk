<?php

namespace App\Bill;

use Illuminate\Support\ServiceProvider;

class BillServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        $view_path = app_path() . DIRECTORY_SEPARATOR . 'Bill' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'Bill');
        $langPath = app_path() . DIRECTORY_SEPARATOR . 'Bill' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($langPath, "Bill");

        $this->mergeConfigFrom(
            app_path('Bill/config/bill.php'), 'bill'
        );

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
        $routes = app_path('/Bill/routes.php');
        if (file_exists($routes)) {
            require $routes;
        }
    }

}
