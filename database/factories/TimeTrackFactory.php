<?php

use App\Model\helpdesk\Ticket\Tickets;
use App\TimeTrack\Models\TimeTrack;
use App\User;
use Faker\Generator as Faker;

$factory->define(TimeTrack::class, function (Faker $faker) {
    return [
        'description' => $faker->paragraph,
        'work_time'   => $faker->randomNumber(3),
        'ticket_id'   => function () {
            return factory(Tickets::class)->create(['user_id' => function () {
                return factory(User::class)->create();
            }, 'status' => 1]);
        },
    ];
});
