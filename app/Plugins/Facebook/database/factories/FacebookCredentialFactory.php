<?php

use App\Plugins\Facebook\Model\FacebookCredential;

$factory->define(FacebookCredential::class, function () {
        return [
            'page_id' => 'my_page_id',
            'page_access_token' => 'my_token',
            'page_name' => 'my_page',
            'new_ticket_interval' => '10',
            'active' => 1
        ];
});
