<?php

namespace App\Model\helpdesk\Form;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;

class FormFieldLabel extends Model
{
	use Observable;

    protected $table = 'form_field_labels';

    /**
     * Fields which are supposed to be hidden
     * NOTE: is_current_language is computed in FormField Model's label relationship. Since frontend do not need to know about this,
     * it has kep hidden
     * @var array
     */
    protected $hidden = ['labelable_id','labelable_type','created_at','updated_at', 'is_current_language', 'meant_for'];

    protected $fillable = ['label','language','flag','meant_for', "description", 'labelable_id', 'labelable_type'];

    protected $appends = ['form_identifier'];

    /**
     * This identifier will be used at frontend to know if it is a form_field, form field option, help topic option, department option or label
     * @return string
     */
    public function getFormIdentifierAttribute()
    {
        return "form_option_".$this->id;
    }

    /**
     * Get all of the owning labelable models.
     */
    public function labelable()
    {
        return $this->morphTo();
    }
}
