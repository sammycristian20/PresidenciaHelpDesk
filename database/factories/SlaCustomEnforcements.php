<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Manage\Sla\SlaCustomEnforcements;

$factory->define(SlaCustomEnforcements::class, function (Faker $faker) {
    return [
        'f_name'  => $faker->name,
        'f_type'  => $faker->name,
        'f_value' => $faker->name,
        'f_label' => $faker->name,
        'sla_id'  => 1
    ];
});
