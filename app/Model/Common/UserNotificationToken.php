<?php 

namespace App\Model\Common;

use Illuminate\Database\Eloquent\Model;

/**
 * @author  avinash kumar <avinash.kumar@ladybirdeb.com>
 */
class UserNotificationToken extends Model
{
    protected $table = "user_notification_tokens";
    
    protected $fillable = [
        /**
         * stores id of user
         */
        'user_id',

        /**
         * stores device/toke type eg fcm_token/browser etc
         */
        'type',

        /**
         * token string for device subscribed by user 
         */
        'token',

        /**
         * send notification only to active tokens
         */
        'active',
    ];

    /**
     * Relation with users
     */
    public function users()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
