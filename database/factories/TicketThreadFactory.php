<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Ticket\Ticket_Thread;

$factory->define(Ticket_Thread::class, function (Faker $faker) {
    return [
        'title'=>$faker->sentence,
        'body'=>$faker->paragraph
    ];
});
