<?php

use Faker\Generator as Faker;
use App\Model\Listener\ListenerEvent;

$factory->define(ListenerEvent::class, function (Faker $faker) {
    return [
		'listener_id' => rand(1, 10),
		'event'       => str_random(15),
		'condition'   => str_random(15),
		'old'         => str_random(15),
		'new'         => str_random(15)
    ];
});
