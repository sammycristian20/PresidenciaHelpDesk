<?php

use App\Plugins\Facebook\Model\FacebookGeneralSettings;

$factory->define(
    FacebookGeneralSettings::class, function () {
    return [
        'fb_secret' => 'secret_agent',
        'hub_verify_token' => 'fb_hub_token'
    ];
});

