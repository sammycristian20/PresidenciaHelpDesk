<?php

namespace App\HelptopicType;

use App\Providers\ExtendServiceProvider as ServiceProvider;

class HelptopicTypeServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {

        $view_path = app_path() . DIRECTORY_SEPARATOR . 'HelptopicType' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'HelptopicType');
        /**
         *language
         */
        $lang_path = app_path() . DIRECTORY_SEPARATOR . 'HelptopicType' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($lang_path, 'HelptopicType');
         // parent::boot('HelptopicType');
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
        $routes = app_path('/HelptopicType/routes.php');
      
        if (file_exists($routes)) {
            require $routes;
        }
    }

}
