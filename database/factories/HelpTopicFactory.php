<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Manage\Help_topic as HelpTopic;

$factory->define(HelpTopic::class, function (Faker $faker) {
    return [
		'topic'             => $faker->word,
		'department'        => 1,
		'ticket_status'     => 1,
		'ticket_num_format' => 1,
		'status'            => 1,
		'type'              => 1,
		'auto_response'     => 1
    ];
});
