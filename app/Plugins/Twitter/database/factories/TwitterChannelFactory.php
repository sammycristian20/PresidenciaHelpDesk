<?php

use App\Plugins\Twitter\Model\TwitterChannel;

$factory->define(TwitterChannel::class, function () {
    
    return [
        'channel'   => 'Twitter',
        'via'       => 'tweet',
        'message_id'=> '1198529298426028032',
        'body'      => 'Sample',
        'user_id'   => '8959u7',
        'ticket_id' => '1',
        'username'  => 'doraemon',
        'posted_at' => '2019-11-19 14:40:26'
    ];
    
});
