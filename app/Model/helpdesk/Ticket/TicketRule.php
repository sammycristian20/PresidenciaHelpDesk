<?php

namespace App\Model\helpdesk\Ticket;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;
use App\Model\helpdesk\Form\FormField;

class TicketRule extends Model
{
    use Observable;

    protected $table = 'ticket_rules';

    protected $fillable = [

      /**
       * Field on which workflow has to be applied
       */
      'field',

      /**
       * Equals, not Equals, contains, not contains etc.
       */
      'relation',

      /**
       * Value of the field
       */
      'value',

      /**
       * If this rule doesn't have a custom field which is parent to someone else,
       * so that while coding for workflow this can be used with checking if it is a parent
       */
      'base_rule',

      /**
       * If this rule belongs to ticket, requester or organization
       */
      'category'
    ];

    /**
     * Get all of the owning reference models.
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * using polymorphic relation for binding
     * @return array      array of rules
     */
    public function rules()
    {
        return $this->morphMany('App\Model\helpdesk\Ticket\TicketRule', 'reference');
    }

    //deletes child data as soon as workflow is deleted
    public function beforeDelete($model)
    {
      //deleting one by one will make sure that it fires delete event in the child model
      foreach ($this->rules as $rule) {
        $rule->delete();
      }
    }

    //deletes child data as soon as workflow is updated, so that parent rule doesn't store wrong child
    public function beforeUpdate($model)
    {
      //deleting one by one will make sure that it fires delete event in the child model
      foreach ($this->rules as $rule) {
        $rule->delete();
      }
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
      $this->attributes['value'] = json_encode($value);
    }

    public function getRelationAttribute($value)
    {
      if(!$value){
        return 'equal';
      }
      return $value;
    }

    public function getCategoryAttribute($value)
    {
        if(!$value){
            return 'ticket';
        }
        return $value;
    }
}
