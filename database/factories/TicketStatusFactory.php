<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Ticket\Ticket_Status;

$factory->define(Ticket_Status::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'message' => str_random(20),
        'visibility_for_client' => 1,
        'allow_client' => 1,
        'visibility_for_agent' => 1,
        'purpose_of_status' => 1,
        'halt_sla' => 1,
        'order' => rand(8, 100),
        'icon' => '#0665f2',
        'icon_color' => 'fa fa-check-circle-o'
        
    ];
});
