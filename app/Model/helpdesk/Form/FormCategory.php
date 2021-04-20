<?php

namespace App\Model\helpdesk\Form;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;
use Illuminate\Database\Eloquent\Collection;

class FormCategory extends Model
{
	use Observable;

    protected $table = 'form_categories';

    protected $fillable = [
        'category',
        'name',
        'type'
    ];

    //gives an array of form fields with same category id
    public function formFields(){
        // return $this->hasMany('App\Model\helpdesk\Form\FormField','category_id','id');
				return $this->morphMany('App\Model\helpdesk\Form\FormField', 'category');
    }

		/**
		 * reference to form group
		 */
		public function formGroups()
		{
			// where will sort
			return $this->belongsToMany('App\Model\helpdesk\Form\FormGroup')
				->withPivot('sort_order','id');
		}
}
