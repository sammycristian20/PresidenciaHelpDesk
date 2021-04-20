<?php 

namespace App\Plugins\SMS;
 
class ServiceProvider extends \App\Plugins\ServiceProvider {
 
    public function register()
    {
        parent::register('SMS');
        //example for registering package of the plugin
        $this->registerProvidersOfPackage([
            \App\Plugins\SMS\Providers\LoginEventServiceProvider::class
        ]);
    }
 
    public function boot()
    {
        /** 
         *View
         */
        $view_path = app_path().DIRECTORY_SEPARATOR.'Plugins'.DIRECTORY_SEPARATOR.'SMS'.DIRECTORY_SEPARATOR.'views';
        $this->loadViewsFrom($view_path, 'SMS');
        parent::boot('SMS');
        
        if (class_exists('Breadcrumbs')) {
            require __DIR__ . '/breadcrumbs.php';
        }
        
        /**
         *language
         */
        $trans = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'SMS' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($trans, 'SMS');
        if (isInstall()) {
            $controller = new Controllers\Msg91SettingsController();
            $controller->activate();
        }
    }
 
}
