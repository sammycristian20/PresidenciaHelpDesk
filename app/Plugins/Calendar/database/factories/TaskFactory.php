<?php

use App\Plugins\Calendar\Model\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'task_start_date' => now(),
        'task_end_date' => now(),
        'task_name' => $faker->name,
        'task_description' => $faker->paragraph,
        'due_alert' => now(),
        'ticket_id' => 1,
        'task_category_id' => 1,
        'task_template_id' => 1
    ];
});
