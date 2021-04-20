<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Agent_panel\Organization;

$factory->define(Organization::class, function (Faker $faker) {


    return [
        'name'=> $faker->company,
        'phone'=> $faker->phoneNumber,
        'website'=> $faker->domainName,
        'address'=>$faker->country,
        'internal_notes'=>$faker->text,
        'domain'=> $faker->domainName,
    ];
});
