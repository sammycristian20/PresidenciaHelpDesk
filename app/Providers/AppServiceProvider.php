<?php

namespace App\Providers;

use App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController;
use App\Model\helpdesk\Settings\Alert;
use App\Model\helpdesk\Settings\Email;
use App\Model\Update\BarNotification;
use Config;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use Laravel\Tinker\TinkerServiceProvider;
use Queue;
use View;
use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Console\InstallCommand;
use Laravel\Passport\Console\KeysCommand;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        // Define Faveo Constants
        $this->defineFaveoConstants();

        $this->app->bind(\Illuminate\Contracts\Auth\Registrar::class);

        require_once __DIR__ . '/../Http/helpers.php';
        /**
         * Registering Dusk and Tinker Service providers when app is not in
         * production environment. Allowing these to be available in
         * local/testing/development environment.
         * Assumtions: For application running in live we will always set envrionment
         * to production
         */
        if (!$this->app->environment('production')) {
            $this->app->register(DuskServiceProvider::class);
            $this->app->register(TinkerServiceProvider::class);
        }

        $this->registerPassportCommands();

        if (isInstall()) {
            $this->module();
            $this->plugin();
        }
    }

    /**
     * Registers passport commands which will be used to generate keys for authentication
     */
    private function registerPassportCommands()
    {
        $this->commands([
           InstallCommand::class,
           ClientCommand::class,
           KeysCommand::class,
        ]);
    }

    public function boot()
    {
        Route::singularResourceParameters(false);

        // Authorize horizon in production
        $this->authorizeHorizon();

        if (isInstall()) {
            $this->observer();
        }
    }

    public function defineFaveoConstants()
    {
        $constants = Config::get('constant');

        // If constants are available
        if (!is_null($constants)) {
            foreach ($constants as $consName => $consValue) {
                if (!defined($consName)) {
                    define($consName, $consValue);
                }
            }
        }
        
        if (is_dir(dirname(__DIR__,1) . DIRECTORY_SEPARATOR . 'Whitelabel')) {
            config(['app.name' => str_replace('Faveo ', '', config('app.name'))]);
        }
    }

    public function composer()
    {
        View::composer('themes.default1.update.notification', function () {
            $notification = [
                'notification' => BarNotification::where('value', '!=', '')->get(),
            ];

            view()->share($notification);
        });
    }

    public function plugin()
    {
        // scanning plugin table for activated plugin
        // cannot use model because it won't be initiated here
        $activatedPlugins = \DB::table('plugins')->where('status', 1)->pluck('name')->toArray();

        foreach ($activatedPlugins as $plugin) {
            $this->app->register("\App\Plugins\\$plugin\ServiceProvider");
        }

        if (isPlugin('DepartmentStatusLink')) {
            $this->app->register(\App\Plugins\DepartmentStatusLink\ServiceProvider::class);
        }
    }

    public function module()
    {
        // White Label module
        if (is_dir(app_path('Whitelabel'))) {
            $this->app->register(\App\Whitelabel\WhitelabelServiceProvider::class);
        }

        // Upgrade module
        if (is_dir(app_path('Upgrade'))) {
            $this->app->register(\App\Upgrade\UpgradeServiceProvider::class);
        }

        // Satellite Helpdesk module
        if (is_dir(app_path('SatelliteHelpdesk'))) {
            $this->app->register(\App\SatelliteHelpdesk\SatelliteHelpdeskProvider::class);
        }

        // Time Track module
        if (is_dir(app_path('TimeTrack')) && isTimeTrack()) {
            $this->app->register(\App\TimeTrack\TimeTrackServiceProvider::class);
        }
    }

    public function observer()
    {
        \App\Model\helpdesk\Ticket\Ticket_Thread::observe(\App\Observers\ThreadObserver::class);
        \App\User::observe(\App\Observers\UserObserver::class);
        \App\Model\helpdesk\Ticket\Tickets::observe(\App\Observers\TicketObserver::class);
        \App\Model\helpdesk\Agent\Department::observe(\App\Observers\DepartmentObserver::class);
        \App\Model\helpdesk\Manage\Help_topic::observe(\App\Observers\HelpTopicObserver::class);
        \App\Model\helpdesk\Ticket\Ticket_Priority::observe(\App\Observers\PriorityObserver::class);
        \App\Model\helpdesk\Ticket\Ticket_Status::observe(\App\Observers\TicketStatusObserver::class);
        \App\Model\helpdesk\Manage\Tickettype::observe(\App\Observers\TicketTypeObserver::class);
        \App\Model\helpdesk\Ticket\Ticket_source::observe(\App\Observers\TicketSourceObserver::class);
        \App\Model\helpdesk\Agent_panel\Organization::observe(\App\Observers\OrganizationObserver::class);
        \App\Model\helpdesk\Agent_panel\OrganizationDepartment::observe(\App\Observers\OrganizationDeptObserver::class);
        \App\Model\helpdesk\Agent\Teams::observe(\App\Observers\TeamObserver::class);
    }

    public function authorizeHorizon()
    {
        // Allow horizon to run in production environment
        \Horizon::auth(function ($request) {
            return true;
        });
    }
}
