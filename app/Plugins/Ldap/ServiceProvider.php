<?php

namespace App\Plugins\Ldap;

class ServiceProvider extends \App\Plugins\ServiceProvider {

    public function register()
    {
        parent::register('Ldap');
    }

    public function boot()
    {
        parent::boot('Ldap');
        if ($this->app->runningInConsole()) {
        	$this->commands([
            	\App\Plugins\Ldap\Console\Commands\SyncLdap::class,
        	]);
    	}
        if (class_exists('Breadcrumbs')) {
            require __DIR__ . '/breadcrumbs.php';
        }

      $trans = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Ldap' . DIRECTORY_SEPARATOR . 'lang';
      $this->loadTranslationsFrom($trans, 'Ldap');

        $view_path = app_path().DIRECTORY_SEPARATOR.'Plugins'.DIRECTORY_SEPARATOR.'Ldap'.DIRECTORY_SEPARATOR.'views';
        $this->loadViewsFrom($view_path, 'Ldap');
    }

}
