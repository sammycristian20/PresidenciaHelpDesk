<?php

namespace App\Plugins\Twitter;

use App\Model\MailJob\Condition;
use App\Plugins\SyncPluginToLatestVersion;


class ServiceProvider extends \App\Plugins\ServiceProvider {

    public function register() {

        parent::register('Twitter');
    }

    public function boot() {

        (new SyncPluginToLatestVersion)->sync('Twitter');

        if ($this->app->runningInConsole()) {
        	$this->commands([
            	\App\Plugins\Twitter\Console\Commands\TicketFetchCommand::class,
        	]);
    	}

        /**
         * Views
         */
        $view_path = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Twitter' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'twitter');

        if (class_exists('Breadcrumbs')) {
            require __DIR__ . '/breadcrumbs.php';
        }

        /**
         * Translation
         */
        $trans = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Twitter' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($trans, 'Twitter');
        
        parent::boot('Twitter');
    }

}
