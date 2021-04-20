<?php

use App\Plugins\Telephony\Model\TelephonyProvider;
use Faker\Generator as Faker;

$factory->define(TelephonyProvider::class, function (Faker $faker) {
    return [
    	'name' => $faker->name,
    	'short' => $faker->name,
    	'iso' => 91,
    ];
});
