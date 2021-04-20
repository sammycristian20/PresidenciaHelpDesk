<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'user_name'      => $faker->userName.str_random(3),
        'first_name'     => $faker->firstName,
        'last_name'      => $faker->lastName,
        'email'          => $faker->unique()->safeEmail,
        'password'       => bcrypt('secret'),
        'remember_token' => str_random(10),
        'agent_tzone'    => 81, //timezone as Asia/kolkata
        'active'         => 1,
        'location'       => null
    ];
});
