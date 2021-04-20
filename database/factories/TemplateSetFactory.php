<?php

use App\Model\Common\TemplateSet;
use Faker\Generator as Faker;

$factory->define(TemplateSet::class, function (Faker $faker) {
    return [
    	'name' => str_random(5),
    	'active' => 1,
    	'template_language' => 'en'
    ];
});
