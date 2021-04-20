<?php

namespace App\Plugins\Reseller;

class ServiceProvider extends \App\Plugins\ServiceProvider {

    public function register() {
        parent::register('Reseller');
    }

    public function boot() {
        /**
         * Views
         */
        $view_path = app_path('Plugins'.DIRECTORY_SEPARATOR.'Reseller'.DIRECTORY_SEPARATOR.'views');
        $this->loadViewsFrom($view_path, 'reseller');
        
        if (isInstall()) {
            $controller = new Controllers\SettingsController();
            $controller->activate();
        }
        
        parent::boot('Reseller');
    }

}
