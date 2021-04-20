<?php namespace App\Plugins\LimeSurvey;
 
class ServiceProvider extends \App\Plugins\ServiceProvider
{
 
    public function register()
    {
        parent::register('LimeSurvey');
    }
 
    public function boot()
    {
        /**
         *View
         */
        $view_path = app_path().DIRECTORY_SEPARATOR.'Plugins'.DIRECTORY_SEPARATOR.'LimeSurvey'.DIRECTORY_SEPARATOR.'views';
        $this->loadViewsFrom($view_path, 'LimeSurvey');
        parent::boot('LimeSurvey');
        
        if (class_exists('Breadcrumbs')) {
            require __DIR__ . '/breadcrumbs.php';
        }
        
        /**
         *language
         */
        $trans = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'LimeSurvey' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($trans, 'LimeSurvey');
        if (isInstall()) {
            $controller = new Controllers\LimeSurveySetting;
            $controller->activate();
        }
    }
}
