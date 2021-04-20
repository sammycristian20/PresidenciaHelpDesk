<?php

use Faker\Generator as Faker;
use App\Model\kb\Article;

$factory->define(Article::class, function (Faker $faker) {
    $categoryName = $faker->unique()->country;//just some word
    return [
        'name'=>$categoryName,
        'slug'=>$categoryName,
        'description'=>$faker->paragraph,
    ];
});
