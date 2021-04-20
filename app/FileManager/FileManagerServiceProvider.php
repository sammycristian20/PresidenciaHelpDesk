<?php

namespace App\FileManager;

use App\FileManager\Services\ACLService\ACLRepository;
use App\FileManager\Services\ConfigService\ConfigRepository;
use App\Providers\ExtendServiceProvider;

class FileManagerServiceProvider extends ExtendServiceProvider
{
    public function boot()
    {
        $migrationPath = app_path() . DIRECTORY_SEPARATOR . 'FileManager' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        $this->loadMigrationsFrom($migrationPath);

        $langPath = app_path() . DIRECTORY_SEPARATOR . 'FileManager' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($langPath, "filemanager");
    }

    public function register()
    {
        $this->app->bind(
            ConfigRepository::class,
            $this->app['config']['file-manager.configRepository']
        );

        // ACL Repository
        $this->app->bind(
            ACLRepository::class,
            $this->app->make(ConfigRepository::class)->getAclRepository()
        );

        if (isInstall()) {
            $routes = app_path('/FileManager/routes.php');
            if (file_exists($routes)) {
                require $routes;
            }
        }
    }
}
