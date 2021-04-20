<?php

namespace App\Plugins\Telephony;

class ServiceProvider extends \App\Plugins\ServiceProvider {

    public function register() {
        parent::register('Telephony');
    }

    public function boot() {
        /**
         * Views
         */
        $view_path = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Telephony' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'telephone');

        if (class_exists('Breadcrumbs')) {
            require __DIR__ . '/breadcrumbs.php';
        }

        /**
         * Translation
         */
        $trans = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Telephony' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($trans, 'Telephony');
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Plugins\Telephony\Console\Commands\CallToTicketConversion::class,
            ]);
        }

        parent::boot('Telephony');
    }

}
