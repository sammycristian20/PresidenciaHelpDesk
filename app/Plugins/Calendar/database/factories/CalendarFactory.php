<?php

use Faker\Generator as Faker;
use App\Plugins\Calendar\Model\Calendar;

$factory->define(Calendar::class, function (Faker $faker) {
    return [
        'task_start_date' => now(),
        'task_end_date' => now(),
        'task_name' => $faker->name,
        'task_description' => $faker->paragraph,
        'due_alert' => now(),
        'ticket_id' => 1,
        'task_list_id' => 1
    ];
});
