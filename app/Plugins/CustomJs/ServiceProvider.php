<?php 

namespace App\Plugins\CustomJs;
 
class ServiceProvider extends \App\Plugins\ServiceProvider {
 
    public function register()
    {
        parent::register('CustomJs');
    }
 
    public function boot()
    {
        /** 
         *View
         */
        $view_path = app_path().DIRECTORY_SEPARATOR.'Plugins'.DIRECTORY_SEPARATOR.'CustomJs'.DIRECTORY_SEPARATOR.'views';
        $this->loadViewsFrom($view_path, 'CustomJs');
        parent::boot('CustomJs');
        
        if (class_exists('Breadcrumbs')) {
            require __DIR__ . '/breadcrumbs.php';
        }
        
        /**
         *language
         */
        $trans = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'CustomJs' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($trans, 'CustomJs');
        if (isInstall()) {
            $controller = new Controllers\SettingsController();
            $controller->activate();
        }
    }
 
}
