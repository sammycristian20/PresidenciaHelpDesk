<?php

use App\Model\helpdesk\Ticket\TicketFilter;
use App\User;
use Faker\Generator as Faker;

$factory->define(TicketFilter::class, function (Faker $faker) {
    return [
        'name'    => $faker->sentence,
        'status'  => 1,
        'user_id' => function () {
            return factory(User::class)->create(['role' => 'agent']);
        },
    ];
});
