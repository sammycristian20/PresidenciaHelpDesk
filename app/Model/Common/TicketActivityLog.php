<?php

namespace App\Model\Common;

use App\Repositories\TicketActivityLogRepository;
use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;

/**
 * @author  avinash kumar <avinash.kumar@ladybirdeb.com>
 */
class TicketActivityLog extends Model
{
    use Observable;

    protected $table = "ticket_activity_logs";

    protected $fillable = [

        /**
         * id of the activity type (for type=ticket, type_id will be ticket_id)
         */
        'ticket_id', // ticket id

        /**
         * name of the field which has changed
         * it will remain empty if something is created or deleted
         */
        'field', // status


        //NOTE: we are not using polymorphic relationship because value of action_taker_type
        //can be system or cron
        /**
         * id of the person who is the cause of the action
         * it will be null in case where action taker does not have a id (for eg system)
         */
        'action_taker_id', // person who is creating the ticket

        /**
         * model of the person who
         */
        'action_taker_type', // user, sla, workflow, listener

        /**
         * final value of the field
         */
        'value',

        /**
         * can be ticket_created, ticket_updated, workflow_enforced, listener_enforced, approval_workflow
         */
        "category",

        /**
         * id of the parent log. Will be null in case of parent
         */
        "parent_id",

        /**
         * A unique string which will help identifying operations that has happened in single request or single artisan command execution
         */
        "identifier"
    ];


    /**
     * Attributes which can be logged
     * @var array
     */
    private $loggableAttributes = ["assigned_id", "type_id", "department_id", "source_id", "location_id", "status_id", "subject", "user_id", "duedate", "priority_id", "help_topic_id", "cc_ids", "label_ids", "tag_ids"];

    /**
     * @removed
     * Tells if a field is loggable or not
     * @param string $field
     * @return bool
     */
    public function isLoggable(string $field)
    {
        // if field has custom_ string, it should be logged
        if (strpos($field, "custom_") !== false) {
            return true;
        }
        
        return in_array($field, $this->loggableAttributes);
    }

    public function ticket()
    {
        return $this->where('type', 'ticket')->belongsTo('App\Model\helpdesk\Ticket\Tickets', 'ticket_id');
    }

    public function children()
    {
        return $this->hasMany(TicketActivityLog::class, "parent_id");
    }

    /**
     * Saves activity
     * @param $ticketId
     */
    public static function saveActivity($ticketId)
    {
        TicketActivityLogRepository::saveActivity($ticketId);
    }


    public function setValueAttribute($value)
    {
        // just storing values of the array in order to avoid index getting converted to object keys.
        // for eg [0 => 'test'] might get converted to {'0': 'test'} after json encoding
        $this->attributes["value"] = is_array($value) ? json_encode(array_values($value)) : json_encode($value);
    }

    public function getValueAttribute($value)
    {
        return json_decode($value);
    }

    public function beforeDelete($model)
    {
        foreach ($model->children as $child) {
            $child->delete();
        }
    }
}
