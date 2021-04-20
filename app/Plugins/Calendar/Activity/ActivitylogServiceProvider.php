<?php

namespace App\Plugins\Calendar\Activity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Plugins\Calendar\Activity\Contracts\Activity;
use App\Plugins\Calendar\Activity\Contracts\Activity as ActivityContract;
use App\Plugins\Calendar\Activity\Exceptions\InvalidConfiguration;
use App\Plugins\Calendar\Activity\Models\Activity as ActivityModel;

class ActivitylogServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind(ActivityLogger::class);

        $this->app->singleton(ActivityLogStatus::class);
    }

    public static function determineActivityModel(): string
    {
        $activityModel = ActivityModel::class;

        if (! is_a($activityModel, Activity::class, true)
            || ! is_a($activityModel, Model::class, true)) {
            throw InvalidConfiguration::modelIsNotValid($activityModel);
        }

        return $activityModel;
    }

    public static function getActivityModelInstance(): ActivityContract
    {
        $activityModelClassName = self::determineActivityModel();

        return new $activityModelClassName();
    }
}
