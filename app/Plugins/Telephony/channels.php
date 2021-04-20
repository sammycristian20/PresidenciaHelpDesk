<?php

use Illuminate\Support\Facades\Broadcast;
/*
|--------------------------------------------------------------------------
| Broadcast Channels for Telephony, channel is being register in 
| BroadcastServiceProvider
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
Broadcast::channel('user-notifications.{id}', function ($user, $id) {
    return true;
});
