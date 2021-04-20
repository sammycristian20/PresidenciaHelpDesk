<?php

namespace App\Plugins\AzureActiveDirectory;

class ServiceProvider extends \App\Plugins\ServiceProvider {

    public function register()
    {
        parent::register('AzureActiveDirectory');
    }

    public function boot()
    {
        parent::boot('AzureActiveDirectory');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Plugins\AzureActiveDirectory\Console\Commands\SyncActiveDirectory::class,
            ]);
        }
        if (class_exists('Breadcrumbs')) {
            require __DIR__ . '/breadcrumbs.php';
        }

        $basePath = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'AzureActiveDirectory';
        $trans = $basePath . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($trans, 'AzureActiveDirectory');

        $viewPath = $basePath. DIRECTORY_SEPARATOR.'views';
        $this->loadViewsFrom($viewPath, 'AzureActiveDirectory');
    }
}
