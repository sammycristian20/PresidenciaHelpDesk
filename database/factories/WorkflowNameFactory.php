<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Workflow\WorkflowName;

$factory->define(WorkflowName::class, function (Faker $faker) {
    return [
		'name'         => $faker->name,
		'status'       => 1,
		'order'        => rand(1, 10),
		'target'       => str_random(10),
		'internal_note'  => $faker->paragraph,
    ];
});
