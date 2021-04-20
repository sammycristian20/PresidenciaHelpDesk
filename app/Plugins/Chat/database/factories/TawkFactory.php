<?php

use App\Plugins\Chat\Model\Chat;

$factory->define(Chat::class, function () {
    return [
        "name" => "Tawk",
        "short" => "tawk",
        'status' => 1,
    ];
    
});
