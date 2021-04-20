<?php

namespace App\Plugins\Chat;

use App\Plugins\SyncPluginToLatestVersion;

class ServiceProvider extends \App\Plugins\ServiceProvider {

    public function register() {
        parent::register('Chat');
    }

    public function boot() {
        /**
         * Views
         */
        $view_path = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Chat' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'chat');

        if (class_exists('Breadcrumbs')) {
            require __DIR__ . '/breadcrumbs.php';
        }

        /**
         * Translation
         */
        $trans = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Chat' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($trans, 'chat');
        
        /**
         * Syncing plugin
         */
        (new SyncPluginToLatestVersion)->sync('Chat');

        parent::boot('Chat');
    }

}
