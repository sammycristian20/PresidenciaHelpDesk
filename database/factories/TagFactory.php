<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Filters\Tag;

$factory->define(Tag::class, function (Faker $faker) {
    return [
        // randomising to so that it doesn't duplicate
        'name' => str_random(5),
        'description' => $faker->paragraph,
    ];
});
