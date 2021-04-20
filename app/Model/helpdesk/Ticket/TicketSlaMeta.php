<?php


namespace App\Model\helpdesk\Ticket;


use App\Model\helpdesk\Manage\Sla\BusinessHours;
use Illuminate\Database\Eloquent\Model;

class TicketSlaMeta extends Model
{
    protected $table = 'ticket_sla_metas';

    public $timestamps = false;

    protected $fillable = [

        /**
         * Id of the associated SLA
         */
        'ticket_sla_id',

        /**
         * the priority which it belongs to
         */
        'priority_id',

        /**
         * time period in which a ticket should be responded
         */
        'respond_within',

        /**
         * time period in which a ticket should be resolved
         */
        'resolve_within',

        /**
         * Business hour with with SLA is linked
         */
        'business_hour_id',

        /**
         * if email notifications should be sent or not
         */
        'send_email_notification',

        /**
         * if app notifications should be sent or not
         */
        'send_app_notification',
    ];

    public function businessHour()
    {
        return $this->hasOne(BusinessHours::class, 'id', 'business_hour_id');
    }

    public function priority()
    {
        return $this->hasOne(Ticket_Priority::class, 'priority_id', 'priority_id');
    }

    public function getRespondWithinAttribute($value)
    {
        if(!$value){
            return "diff::4~hour";
        }
        return $value;
    }

    public function getResolveWithinAttribute($value)
    {
        if(!$value){
            return "diff::10~hour";
        }
        return $value;
    }

    public function setBusinessHourIdAttribute($value)
    {
        $this->attributes["business_hour_id"] = $value ?: BusinessHours::where("is_default", 1)->value("id");
    }
}