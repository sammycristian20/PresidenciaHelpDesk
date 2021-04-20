<?php

namespace App\Model\helpdesk\Settings;

use App\BaseModel;
use App\Model\helpdesk\Form\FormField;

class CommonSettings extends BaseModel
{
    protected $table = 'common_settings';
    protected $fillable = [
        'status', 'option_name', 'option_value', 'optional_field', 'created_at', 'updated_at',
    ];

    public function getStatus($option_name){
        $status ="";
        $schema = $this->where('option_name',$option_name)->first();
        if($schema){
            $status = $schema->status;
        }
        return $status;
    }

    public function getOptionValue($option,$field=''){
         $schema = $this->where('option_name',$option);
         if($field!=""){
             $schema = $schema->where('optional_field',$field);
             return $schema->first();
         }
         $value = $schema->get();
         return $value;
    }

    public function setStatusAttribute($value)
    {
      if($this->option_name == 'micro_organization_status'){
        FormField::where('title','Organisation Department')->update(['is_active'=>$value]);
      }
      $this->attributes['status'] = $value;
    }
}
