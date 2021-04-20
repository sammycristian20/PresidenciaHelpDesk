<?php

namespace App\Model\helpdesk\Form;

use App;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;

class FormFieldOption extends Model
{
    use Observable;

    protected $table = 'form_field_options';

    protected $hidden = ['form_field_id','created_at','updated_at'];

    protected $fillable = ['form_field_id','value', 'sort_order'];

    protected $appends = ['form_identifier', 'label'];

    /**
     * This identifier will be used at frontend to know if it is a form_field, form field option, help topic option, department option or label
     * @return string
     */
    public function getFormIdentifierAttribute()
    {
        return "form_option_".$this->id;
    }

    public function formFields()
    {
        //one to many relationship
        return $this->belongsTo('App\Model\helpdesk\Form\FormField', 'form_field_id');
    }


    /**
     * Gives the label for the option
     * @return mixed
     */
    public function getLabelAttribute()
    {
        // Since label doesn't have a language option, just picking the first label should get the job done
        return $this->labels()->value('label');
    }

    /**
     * using polymorphic relation for binding
     */
    public function labels()
    {
        $lang = App::getLocale();

        // adding an orderBy, so that the label with current language can be at top
        return $this->morphMany('App\Model\helpdesk\Form\FormFieldLabel', 'labelable')
            ->select("form_field_labels.*", DB::raw("(CASE when language='$lang' THEN 1 ELSE 0 END) as is_current_language"))
            ->orderBy("is_current_language", "desc")
            ->orderBy("id", "asc")
            ->where('meant_for', 'option');
    }

    /**
     * maps to all the formField entries embedded inside nodes
     */
    public function nodes()
    {
        return $this->hasMany('App\Model\helpdesk\Form\FormField', 'option_id', 'id');
    }

    private function beforeDelete($model)
    {
        foreach ($model->labels as $label) {
            $label->delete();
        }

        //deleting one by one will make sure that it fires delete event in the child model
        foreach ($this->nodes as $node) {
            $node->delete();
        }
    }

    public function formGroups()
    {
        return $this->belongsToMany('App\Model\helpdesk\Form\FormGroup')->withPivot('sort_order', 'id');
    }
}
