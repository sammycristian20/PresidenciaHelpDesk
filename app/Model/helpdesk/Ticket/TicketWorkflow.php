<?php

namespace App\Model\helpdesk\Ticket;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;

class TicketWorkflow extends Model
{
    use Observable;

    protected $table = 'ticket_workflows';

    protected $fillable = [

      /**
       * name of the workflow
       */
      'name',

      /**
       * If status is active or not
       */
      'status',

      /**
       * from which source tickets are targetted in the workflow
       */
      'target',

      /**
       * Order of the workflow
       */
      'order',

      /**
       * All or any action match triggers workflow
       */
      'matcher',

      /**
       * internal notes for description purpose
       */
      'internal_notes'];

    /**
     * using polymorphic relation for binding
     * @return array      array of rules
     */
    public function rules()
    {
        return $this->morphMany('App\Model\helpdesk\Ticket\TicketRule', 'reference');
    }

    /**
     * using polymorphic relation for binding
     * @return array      array of actions
     */
    public function actions()
    {
        return $this->morphMany('App\Model\helpdesk\Ticket\TicketAction', 'reference');
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
        $data    = \App\Model\helpdesk\Ticket\Ticket_source::where('id',$value)->select('id','name')->first();
        return $data;
    }

    public function getStatusAttribute($value)
    {
      return (bool)$value;
    }
}
