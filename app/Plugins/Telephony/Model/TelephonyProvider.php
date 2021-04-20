<?php

namespace App\Plugins\Telephony\Model;

use Illuminate\Database\Eloquent\Model;

class TelephonyProvider extends Model
{
    protected $table = "telephony_provider_settings";
    protected $fillable = [
        /**
         * Primary id
         */
        'id',

        /**
         * Name of the IVR provider
         */
        'name',

        /**
         * Short name of the IVR provider
         */
        'short',

        /**
         * API/APP id if required for configuration of the IVR provider
         */
        'app_id',

        /**
         * API token if required for configuration of the IVR provider
         */
        'token',

        /**
         * Default ISO code to get formatted phone number using libphonenumber PHP library
         */
        'iso',

        /**
         * Shall the miscalls be logged or not for the IVR provider
         */
        'log_miss_call',

        /**
         * Waiting time before call log get converted to a ticket
         */
        'conversion_waiting_time',
    ];

    public function getRouteKeyName()
    {
        return 'short';
    }
}
