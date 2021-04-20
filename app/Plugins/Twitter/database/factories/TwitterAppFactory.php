<?php

use App\Plugins\Twitter\Model\TwitterApp;
use Faker\Generator as Faker;

$factory->define(TwitterApp::class, function (Faker $faker) {
    return [
        'consumer_api_secret' => $faker->name,
        'consumer_api_key'    => $faker->sentence,
        'access_token'        => $faker->sentence,
        'access_token_secret' => $faker->sentence,
        'hashtag_text'        => $faker->sentence,
        'reply_interval'      => 1,
    ];
    
});
