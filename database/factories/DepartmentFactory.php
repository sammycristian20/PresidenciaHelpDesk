<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Agent\Department;

$factory->define(Department::class, function (Faker $faker) {
    return [
        'name'=>$faker->name
    ];
});
