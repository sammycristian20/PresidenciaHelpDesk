<?php

namespace App\Model\helpdesk\Ticket;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;
use Illuminate\Support\Collection;
use Lang;

class TicketListener extends Model
{
    use Observable;

    protected $table = 'ticket_listeners';

    protected $fillable = [
      /**
       * name of the listener
       */
      'name',

      /**
       * Agent or client
       */
      'triggered_by',

      /**
       * from which source tickets are targetted in the listener
       */
      'target',

      /**
       * If status is active or not
       */
      'status',

      /**
       * Order of the listener
       */
      'order',

      /**
       * Any or all
       */
      'matcher',

      /**
       * internal notes for description purpose
       */
      'internal_notes'
    ];

    public function rules()
    {
        return $this->morphMany('App\Model\helpdesk\Ticket\TicketRule', 'reference');
    }

    public function actions()
    {
        return $this->morphMany('App\Model\helpdesk\Ticket\TicketAction', 'reference');
    }

    public function events()
    {
        return $this->hasMany('App\Model\helpdesk\Ticket\TicketEvent');
    }

    //deletes child data as soon as workflow is deleted
    public function beforeDelete($model)
    {
        foreach ($this->rules as $rule) {
            $rule->delete();
        }

        foreach ($this->actions as $action) {
            $action->delete();
        }
    }

    public function getTargetAttribute($value)
    {
        $data    = \App\Model\helpdesk\Ticket\Ticket_source::where('id', $value)->select('id', 'name')->first();
        return $data;
    }

    public function getStatusAttribute($value)
    {
        return (bool)$value;
    }

    /**
     * Gets list of available events in the listener
     * @return Collection
     */
    public static function getEventList() : Collection
    {
        return new Collection([
            (object)['id'=>'priority_id', 'name'=> Lang::get('lang.priority_is_changed'), 'type'=> 'api', 'api_info'=>'url:=/api/enforcer-events/dependency/priorities;;'],
            (object)['id'=>'type_id', 'name'=> Lang::get('lang.type_is_changed'), 'type'=> 'api', 'api_info'=>'url:=/api/enforcer-events/dependency/types;;'],
            (object)['id'=>'status_id', 'name'=> Lang::get('lang.status_is_changed'), 'type'=> 'api', 'api_info'=>'url:=/api/enforcer-events/dependency/statuses;;'],
            (object)['id'=>'help_topic_id', 'name'=> Lang::get('lang.helptopic_is_changed'), 'type'=> 'api', 'api_info'=>'url:=/api/enforcer-events/dependency/help-topics;;'],
            (object)['id'=>'department_id', 'name'=> Lang::get('lang.department_is_changed'), 'type'=> 'api', 'api_info'=>'url:=/api/enforcer-events/dependency/departments;;'],
            (object)['id'=>'assigned_id', 'name'=> Lang::get('lang.agent_is_changed'), 'type'=> 'api', 'api_info'=>'url:=/api/enforcer-events/dependency/agents;;'],
            (object)['id'=>'note', 'name'=> Lang::get('lang.note_is_added'), 'type'=> null],
            (object)['id'=>'reply', 'name'=> Lang::get('lang.reply_is_sent'), 'type'=> null],
            (object)['id'=>'duedate', 'name'=> Lang::get('lang.duedate_is_changed'), 'type'=> null],
        ]);
    }
}
