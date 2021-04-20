<?php

namespace App\Plugins\Whatsapp;

use App\Model\MailJob\Condition;
use App\Plugins\SyncPluginToLatestVersion;


class ServiceProvider extends \App\Plugins\ServiceProvider {

    public function register() {

        parent::register('Whatsapp');
    }

    public function boot() {

        (new SyncPluginToLatestVersion)->sync('Whatsapp');


        if ($this->app->runningInConsole()) {
        	$this->commands([
            	\App\Plugins\Whatsapp\Console\Commands\WhatsappProcess::class,
        	]);
    	}

        /**
         * Views
         */
        $view_path = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Whatsapp' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($view_path, 'Whatsapp');

        if (class_exists('Breadcrumbs')) {
            require __DIR__ . '/breadcrumbs.php';
        }

        /**
         * Translation
         */
        $trans = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Whatsapp' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($trans, 'Whatsapp');
        
        //Adding whatsapp as a source for ticket
        if(!\DB::table('ticket_source')->where('name','Whatsapp')->first()) {
            \DB::table('ticket_source')->insert([
                'name' => "Whatsapp",
                'value'=> "Whatsapp",
                'css_class'=>'fab fa-whatsapp',
                'location' => "",
                'is_default' => 1

            ]);
        }

        parent::boot('Whatsapp');
    }

}
