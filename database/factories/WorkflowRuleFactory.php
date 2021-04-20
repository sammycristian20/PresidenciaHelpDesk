<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Workflow\WorkflowRules;

$factory->define(WorkflowRules::class, function (Faker $faker) {
    return [
		'workflow_id'       => rand(1, 10),
		'matching_criteria' => str_random(15),
		'matching_scenario' => str_random(15),
		'matching_relation' => str_random(15),
		'matching_value'    => str_random(15),
		'custom_rule'       => null
    ];
});
