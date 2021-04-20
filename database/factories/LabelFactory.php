<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Filters\Label;

$factory->define(Label::class, function (Faker $faker) {
    return [
        'title' => $faker->name,
        'color' => $faker->hexcolor,
        'order' => $faker->unique()->randomDigit,
        'visible_to' => "all",
    ];
});
