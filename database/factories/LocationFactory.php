<?php

use App\Location\Models\Location;
use Faker\Generator as Faker;

$factory->define(Location::class, function (Faker $faker) {
    return [
        'title'          => $faker->title,
    ];
});
