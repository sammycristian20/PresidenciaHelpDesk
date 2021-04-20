<?php

use Faker\Generator as Faker;
use App\Model\MailJob\Condition;

$factory->define(Condition::class, function (Faker $faker) {
    return [
		'job'         =>  str_random(5),
		'value'       =>  str_random(5),
		'icon'        =>  str_random(5),
		'command'     =>  str_random(5),
		'job_info'    =>  str_random(5),
    'plugin_name' =>  null,
		'active'      =>  1,
		'plugin_job'  =>  0,
	];
});
