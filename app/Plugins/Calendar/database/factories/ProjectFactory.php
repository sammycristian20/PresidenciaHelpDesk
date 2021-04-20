<?php

use Faker\Generator as Faker;
use App\Plugins\Calendar\Model\Project;

$factory->define(Project::class, function (Faker $faker) {
    return [
        'name' => $faker->name
    ];
});
