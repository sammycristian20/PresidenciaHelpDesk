<?php 

namespace App\Plugins\SMS\Providers;

use App\Providers\EventServiceProvider;

class LoginEventServiceProvider extends EventServiceProvider
{
	/**
     * Register any other events for your application.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return void
     */
    public function boot()
    {
        // dummy provider for checking custom package registration
    	array_push($this->listen["Illuminate\Auth\Events\Login"], "App\Plugins\SMS\Listeners\RequiredVerifiedMobile");
    	parent::boot();  	
    }
}
