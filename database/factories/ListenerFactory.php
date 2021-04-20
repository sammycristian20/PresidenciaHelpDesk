<?php

use Faker\Generator as Faker;
use App\Model\Listener\Listener;

$factory->define(Listener::class, function (Faker $faker) {
    return [
    	
		'name'         => $faker->name,
		'status'       => 1,
		'description'  => $faker->paragraph,
		'performed_by' => 'agent',
		'order'        => rand(1, 10),
		'rule_match'   => 'any'
    ];
});
