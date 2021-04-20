<?php

namespace App\Plugins\Calendar;

use App\Plugins\Calendar\Activity\ActivitylogServiceProvider;
use App\Plugins\Calendar\Console\Commands\TaskReminder;
use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Observers\TaskObserver;
use App\Plugins\Calendar\Policies\TaskPolicy;
use App\Plugins\SyncPluginToLatestVersion;
use Illuminate\Support\Facades\Gate;

class ServiceProvider extends \App\Plugins\ServiceProvider
{
    protected $policies = [
        Task::class => TaskPolicy::class
    ];

    public function register()
    {
        parent::register('Calendar');
    }

    public function boot()
    {
        (new SyncPluginToLatestVersion())->sync('Calendar');
        /**
         *View
         */
        if ($this->app->runningInConsole()) {
            $this->commands([TaskReminder::class]);
        }

        $viewPath = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Calendar' . DIRECTORY_SEPARATOR . 'views';
        $this->loadViewsFrom($viewPath, 'Calendar');


        if (class_exists('Breadcrumbs')) {
            require __DIR__ . '/breadcrumbs.php';
        }

        /**
         *language
         */
        $trans = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'Calendar' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom($trans, 'Calendar');

        $this->registerPolicies();

        $this->app->register(ActivitylogServiceProvider::class);

        parent::boot('Calendar');
    }

    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }
}
