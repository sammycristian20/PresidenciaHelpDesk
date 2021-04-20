<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Agent_panel\User_org;

$factory->define(User_org::class, function (Faker $faker) {
    return [
        'org_id' => rand(1, 100),
        'user_id' => rand(1, 100),
        'role' => 'member',
        'org_department' => null
    ];
});
