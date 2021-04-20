<?php

namespace App\FaveoLog;

use App\Providers\ExtendServiceProvider as ServiceProvider;

class LaravelLogViewerServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        $migrationPath = app_path() . DIRECTORY_SEPARATOR . 'FaveoLog' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        $this->loadMigrationsFrom($migrationPath);

        $view_path = app_path() . DIRECTORY_SEPARATOR . 'FaveoLog' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'log');

        $lang_path = app_path() . DIRECTORY_SEPARATOR . 'FaveoLog' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($lang_path, "log");

        $this->commands([
            \App\FaveoLog\Console\Commands\DeleteLogs::class,
        ]);

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
        $routes = app_path('/FaveoLog/routes.php');
        if (file_exists($routes)) {
            require $routes;
        }
    }
}
