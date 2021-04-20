<?php

use App\Plugins\Telephony\Model\TelephonyLog;
use Faker\Generator as Faker;

$factory->define(TelephonyLog::class, function (Faker $faker) {
    return [
        /**
         * Call Id in IVR system
         */
        'call_id' => $faker->name,
        
        /**
         * Number from which call received
         */
        'call_from' => $faker->phoneNumber,
        
        /**
         * Number on which call received
         */
        'call_to' => $faker->phoneNumber,
        
        /**
         * Call recording if available
         */
        'recording' => $faker->url,
        
        /**
         * Number on which call is connected
         */
        'connecting_to' => $faker->phoneNumber,
        
        /**
         * Status of call
         */
        'call_status' => $faker->name,
        
        /**
         * Call start date
         */
        'call_start_date' => now(),
        
        /**
         * Call finish date
         */
        'call_end_date' => now(),
        
        /**
         * Faveo user id of caller
         */
        'caller_user_id' => null,
        
        /**
         * Faveo user id of receiver
         */
        'receiver_user_id' => null,
        
        /**
         * Id of Telephony provider
         */
        'provider_id' => 1,
        
        /**
         * Id of ticket linked to the call
         */
        'call_ticket_id' => null,

        /**
         * Id of intended department 
         */
        'intended_department_id' => null,
        
        /**
         * Id of intended helptopic
         */
        'intended_helptopic_id' => null,

        /**
         * Id of intended helptopic
         */
        'notes' => $faker->text
    ];
});
