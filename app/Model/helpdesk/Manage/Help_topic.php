<?php

namespace App\Model\helpdesk\Manage;

use App\BaseModel;
use App\Model\helpdesk\Settings\System;

class Help_topic extends BaseModel
{

    protected $table    = 'help_topic';
    protected $fillable = [
        'id', 'topic', 'parent_topic', 'custom_form', 'department', 'ticket_status', 'priority',
        'sla_plan', 'thank_page', 'ticket_num_format', 'internal_notes', 'status', 'type', 'auto_assign',
        'auto_response', 'nodes', 'linked_departments'
    ];

    protected $appends = ['form_identifier'];

    /**
     * This identifier will be used at frontend to know if it is a form_field, form field option, help topic option, department option or label
     * @return string
     */
    public function getFormIdentifierAttribute()
    {
        return "help_topic_".$this->id;
    }

    public function department()
    {
        $related    = 'App\Model\helpdesk\Agent\Department';
        $foreignKey = 'department';
        return $this->belongsTo($related, $foreignKey);
    }

    public function delete()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        parent::delete();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    //gives an array of form fields with same category id
    public function nodes(){
        // return $this->hasMany('App\Model\helpdesk\Form\FormField','category_id','id');
				return $this->morphMany('App\Model\helpdesk\Form\FormField', 'category');
    }

    public function getLinkedDepartmentsAttribute($value)
    {
        if(!$value){
          // if null, give all departments
          return isset($this->attributes['department']) ? $this->attributes['department'] : null;
        }
        return $value;
    }

		/**
		 * reference to form group
		 */
		public function formGroups()
		{
			// where will sort
			return $this->belongsToMany('App\Model\helpdesk\Form\FormGroup','help_topic_form_group')
        ->withPivot('sort_order','id');
		}
}
