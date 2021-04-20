<?php

use App\Plugins\Calendar\Model\TaskCategory;
use App\Plugins\Calendar\Model\TaskTemplate;
use Faker\Generator as Faker;

$factory->define(
    TaskTemplate::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->paragraph,
        'category_id' => factory(TaskCategory::class)->create()->id
    ];
});
