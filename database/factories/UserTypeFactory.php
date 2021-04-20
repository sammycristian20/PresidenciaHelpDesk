<?php

use App\Model\helpdesk\Manage\UserType;
use Faker\Generator as Faker;

$factory->define(UserType::class, function (Faker $faker) {
	$text = $faker->text(50);
    return [
        'name' => $text,
        'key' => ucFirst($text)
    ];
});
