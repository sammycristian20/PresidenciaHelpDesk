<?php

use Faker\Generator as Faker;
use App\Model\Listener\ListenerAction;

$factory->define(ListenerAction::class, function (Faker $faker) {
    return [
		'listener_id'   => rand(1, 10),
		'key'           => str_random(15),
		'value'         => str_random(15),
		'custom_action' => null
    ];
});
