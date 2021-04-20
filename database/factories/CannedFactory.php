<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Agent_panel\Canned;

$factory->define(Canned::class, function (Faker $faker) {
    return [
        'title'=>$faker->word,
        'message'=>$faker->sentence
    ];
});
