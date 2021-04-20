<?php

namespace App\Plugins\Migration;

class ServiceProvider extends \App\Plugins\ServiceProvider {

    public function register() {
        parent::register('Migration');
    }

    public function boot() {
        /**
         * Views
         */
        $view_path = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Migration' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'migration');

//        if (class_exists('Breadcrumbs')) {
//            require __DIR__ . '/breadcrumbs.php';
//        }

        /**
         * Translation
         */
        $trans = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Migration' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($trans, 'migration');
        
       

        parent::boot('Migration');
    }

}
