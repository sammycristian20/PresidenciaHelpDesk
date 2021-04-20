<?php

namespace App\Plugins\Telephony\Model;

use Illuminate\Database\Eloquent\Model;

class TelephonyLog extends Model
{
    protected $table = "telephony_call_logs";
    protected $fillable = [
        /**
         * Call log id
         */
        'id',
        
        /**
         * Call Id in IVR system
         */
        'call_id',
        
        /**
         * Number from which call received
         */
        'call_from',
        
        /**
         * Number on which call received
         */
        'call_to',
        
        /**
         * Call recording if available
         */
        'recording',
        
        /**
         * Number on which call is connected
         */
        'connecting_to',
        
        /**
         * Status of call
         */
        'call_status',
        
        /**
         * Call start date
         */
        'call_start_date',
        
        /**
         * Call finish date
         */
        'call_end_date',
        
        /**
         * Faveo user id of caller
         */
        'caller_user_id',
        
        /**
         * Faveo user id of receiver
         */
        'receiver_user_id',
        
        /**
         * Id of Telephony provider
         */
        'provider_id',
        
        /**
         * Id of ticket linked to the call
         */
        'call_ticket_id',

        /**
         * Id of intended department 
         */
        'intended_department_id',
        
        /**
         * Id of intended helptopic
         */
        'intended_helptopic_id',

        /**
         * Id of intended helptopic
         */
        'notes',

        /**
         * If log is not linked to a ticket is conversion job dispatched 
         */
        'job_dispatched',

        /**
         * Is converted manually or autmoatically
         */
        'auto_conversion'
    ];

    public function getRouteKeyName()
    {
        return 'call_id';
    }
}
