<?php

use App\Plugins\Calendar\Model\TaskCategory;
use Faker\Generator as Faker;

$factory->define(TaskCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'project_id' => factory(\App\Plugins\Calendar\Model\Project::class)->create()->id
    ];
});
