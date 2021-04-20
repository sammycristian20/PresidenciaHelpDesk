<?php namespace App\Plugins\Envato;
 
class ServiceProvider extends \App\Plugins\ServiceProvider {
 
    public function register()
    {
        parent::register('Envato');
    }
 
    public function boot()
    {
        $view_path = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Envato' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'envato');
        if (class_exists('Breadcrumbs')) {
            require __DIR__ . '/breadcrumbs.php';
        }
        $control = new Controllers\TicketFormController();
        $control->CreateTables();
        parent::boot('Envato');
    }
 
}
