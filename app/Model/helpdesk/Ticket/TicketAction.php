<?php

namespace App\Model\helpdesk\Ticket;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;
use App\Model\helpdesk\Form\FormField;

class TicketAction extends Model
{
    use Observable;

    protected $table = 'ticket_actions';

    protected $fillable = ['field','value','base_rule'];

    /**
     * Get all of the owning reference models.
     */
    public function reference()
    {
        return $this->morphTo();
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
      //deleting one by one will make sure that it fires delete event in the child model
      foreach ($this->actions as $action) {
        $action->delete();
      }
    }

    //deletes child data as soon as workflow is updated, so that parent action doesn't store wrong child
    public function beforeUpdate($model)
    {
      //deleting one by one will make sure that it fires delete event in the child model
      foreach ($this->actions as $action) {
        $action->delete();
      }
    }

    /**
     * It allows to create an action in which sending multiple emails is possible
     */
    public function actionEmail(){
        return $this->hasOne('App\Model\helpdesk\Ticket\TicketActionEmail');
    }

    public function getValueAttribute($value)
    {
      try{
          return json_decode($value);

      }catch(\Exception $e){

        return $value;
      }
    }

    public function setValueAttribute($value)
    {
      // array has to be converted into comma seperated value
      $this->attributes['value'] = json_encode($value);
    }

}
