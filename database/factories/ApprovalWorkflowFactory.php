<?php

use App\Model\helpdesk\Workflow\ApprovalWorkflow;
use App\User;
use Faker\Generator as Faker;

$factory->define(ApprovalWorkflow::class, function (Faker $faker) {
    return [
        'name'    => $faker->sentence,
        'user_id' => factory(User::class)->create(),
        'type' => 'approval_workflow',
    ];
});
