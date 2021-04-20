<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Workflow\WorkflowAction;

$factory->define(WorkflowAction::class, function (Faker $faker) {
    return [
		'workflow_id'   => rand(1, 10),
		'condition'     => str_random(15),
		'action'        => str_random(15),
		'custom_action' => null
    ];
});
