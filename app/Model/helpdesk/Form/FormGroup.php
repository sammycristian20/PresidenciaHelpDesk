<?php

namespace App\Model\helpdesk\Form;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;
use Event;

class FormGroup extends Model
{
	  use Observable;

    protected $table = 'form_groups';

    protected $fillable = [
        /**
         * Name of the group
         */
        'name',

        /**
         * If a group is active. It can be used by a plugin to activate and deactivate a group
         */
        'active',
        /**
         * group type
         */
        'group_type'

    ];

    protected $hidden = ['active', 'pivot'];

    protected $appends = ['form_identifier', 'edit_url'];


    /**
     * This identifier will be used at frontend to know if it is a form_field, form field option, help topic option, department option or label
     * @return string
     */
    public function getFormIdentifierAttribute()
    {
        return "form_group_".$this->id;
    }


    public function formFields(){
				return $this->hasMany('App\Model\helpdesk\Form\FormField');
    }

    public function beforeDelete($model)
    {
        //deleting one by one will make sure that it fires delete event in the child model,
        //so that nested field can be deleted
        foreach ($this->formFields as $formField) {
          $formField->delete();
        }
    }

		public function helpTopics()
		{
			return $this->belongsToMany('App\Model\helpdesk\Manage\Help_topic', 'help_topic_form_group','form_group_id','help_topic_id');
		}

		public function departments()
		{
			return $this->belongsToMany('App\Model\helpdesk\Agent\Department', 'department_form_group','form_group_id','department_id');
		}

    /**
     * This edit_url will be used in frontend to handle formgroup edit url
     * @return string
     */
    public function getEditUrlAttribute()
    {
        $otherUrlPrefix = NULL;
        // updating prefix for edit view URL
        Event::dispatch('update-other-prefix-url', [&$otherUrlPrefix]);
        $editUrl = implode('', ['/form-group/edit/', $this->id]);
        if ($this->group_type != 'ticket') {
            $editUrl = implode('', [$otherUrlPrefix, $editUrl]);
        }
        return $editUrl;
    }
}
