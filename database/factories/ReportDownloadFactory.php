<?php

use App\FaveoReport\Models\ReportDownload;
use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(ReportDownload::class, function (Faker $faker) {
    return [
        'file'         => $faker->word,
        'ext'          => 'xls',
        'type'         => $faker->word,
        'hash'         => $faker->sha1,
        'expired_at'   => Carbon::now()->addHour(),
        'is_completed' => 1,
        'user_id'      => function () {
            return factory(User::class)->create();
        },
    ];
});
