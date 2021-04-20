<?php

namespace App\Model\helpdesk\Ticket;

use App\BaseModel;
use App\Model\Custom\Required;

class Ticket_Form_Data extends BaseModel {

    protected $table = 'ticket_form_data';

    protected $fillable = ['ticket_id', 'title', 'content','key'];

    protected $appends = ['label'];
    
    public function getKeyRelation()
    {
        return $this->belongsTo('App\Model\Custom\Required', 'key', 'field');
    }

    public function getLabelAttribute()
    {
    	$labelObj = Required::where('field',$this->key)->first();
 		if($labelObj){
 			return $labelObj->label;
 		}
 		return null;
    }

}
