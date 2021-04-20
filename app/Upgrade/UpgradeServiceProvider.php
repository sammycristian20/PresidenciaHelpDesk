<?php 

namespace App\Upgrade;
 
use App\Providers\ExtendServiceProvider as ServiceProvider;

class UpgradeServiceProvider extends ServiceProvider {
  
    public function boot()
    {
        
        // if (class_exists('Breadcrumbs')) {
        //     require __DIR__ . '/breadcrumbs.php';
        // }
        
        /**
         *language
         */
        $trans = app_path() . DIRECTORY_SEPARATOR . 'Upgrade' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($trans, 'Upgrade');
        
        // if (isInstall()) {
        //     $controller = new Controllers\UpgradeController();
        //     $controller->activate();
        // }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        // Add routes
        $routes = app_path('/Upgrade/routes.php');
        if (file_exists($routes)) {
            require $routes;
        }
    }
 
}
