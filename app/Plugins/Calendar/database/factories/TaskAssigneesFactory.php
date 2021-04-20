<?php

use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\TaskAssignees;
use Faker\Generator as Faker;

$factory->define(
    TaskAssignees::class, function (Faker $faker) {
    return [
        'task_id' => factory(Task::class),
        'user_id' => rand(1,10)
    ];
});
