<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\TicketRecur\Recur;

$factory->define(Recur::class, function (Faker $faker) {
    return [
        'name' => str_random(15),
        'delivery_on' => $faker->dayOfWeek,
        'interval' => 'weekly',
    ];
});
