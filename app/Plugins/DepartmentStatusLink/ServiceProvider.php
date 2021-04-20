<?php

namespace App\Plugins\DepartmentStatusLink;

class ServiceProvider extends \App\Plugins\ServiceProvider
{
    public function register()
    {
        parent::register('DepartmentStatusLink');
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'views', 'DepartmentStatusLink');

        $this->loadTranslationsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'lang', 'DepartmentStatusLink');

    }

}
