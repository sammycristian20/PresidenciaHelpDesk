<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Settings\Plugin;

$factory->define(Plugin::class, function (Faker $faker) {
    return [
        'name'=>$faker->name,
        'path'=>str_random(15),
        'status'=>1
    ];
});
