<?php

use App\Model\helpdesk\Ticket\Tickets;
use Faker\Generator as Faker;

$factory->define(Tickets::class, function (Faker $faker) {
    \DB::statement('SET FOREIGN_KEY_CHECKS=0');
    return [
    	  'ticket_number' => str_random(10),
        'user_id' => 1,
        'sla' => '2',
        'dept_id'=>'1',
        'help_topic_id'=>1,
        'sla'=>1,
        'type'=>1,
        'source'=>1,
        'priority_id'=>'1',
        'status' => 1,
        'closed_at'=>null,
        'location_id' => null,
    ];
});
