<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Settings\CommonSettings;

$factory->define(CommonSettings::class, function (Faker $faker) {
    return [
        'status' => 1,
        'option_name' => $faker->name,
        'option_value' => $faker->name,
        'optional_field' => $faker->name
    ];
});
