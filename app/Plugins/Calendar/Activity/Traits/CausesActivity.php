<?php

namespace App\Plugins\Calendar\Activity\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Plugins\Calendar\Activity\ActivitylogServiceProvider;

trait CausesActivity
{
    public function actions(): MorphMany
    {
        return $this->morphMany(
            ActivitylogServiceProvider::determineActivityModel(),
            'causer'
        );
    }
}
