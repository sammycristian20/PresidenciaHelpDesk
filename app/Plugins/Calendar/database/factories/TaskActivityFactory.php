<?php

use App\Plugins\Calendar\Activity\Models\Activity;
use App\Plugins\Calendar\Model\Task;
use Faker\Generator as Faker;

$factory->define(
    Activity::class, function (Faker $faker) {
    return [
        'log_name' => 'task',
        'description' => 'created',
        'subject_id' => 0,
        'subject_type' => 'App\Plugins\Calendar\Model\Task',
        'causer_id' => 1,
        'causer_type' => 'App\User',
        'properties' => collect([
            'attributes' => [
                'task_name' => 'Factory Task'
            ]
        ])
    ];
});