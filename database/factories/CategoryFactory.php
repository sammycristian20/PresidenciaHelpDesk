<?php

use Faker\Generator as Faker;
use App\Model\kb\Category;

$factory->define(Category::class, function (Faker $faker) {
    $categoryName = $faker->unique()->country;//just some word
    return [
        'name'=>$categoryName,
        'slug'=>'category',
        'description'=>$faker->paragraph,
        'status'=>1,
        'display_order'=>$faker->unique()->randomDigit,
    ];
});
