<?php

use App\Model\helpdesk\Ticket\Ticket_Collaborator;
use Faker\Generator as Faker;

$factory->define(Ticket_Collaborator::class, function (Faker $faker) {
    return [
    	'isactive' => 1,
        'user_id' => str_random(100),
        'role' => 'test',
        'ticket_id' => str_random(100),
    ];
});
