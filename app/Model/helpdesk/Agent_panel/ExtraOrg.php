<?php

namespace App\Model\helpdesk\Agent_panel;

use Illuminate\Database\Eloquent\Model;

class ExtraOrg extends Model
{
    protected  $table = "extra_orgs";
    protected  $fillable =['org_id','key','value'];
    
    public function organization(){
        return $this->belongsTo('App\Model\helpdesk\Agent_panel\Organization','org_id');
    }

    public function getKeyRelation()
    {
        return $this->belongsTo('App\Model\Custom\Required', 'key', 'field');
    }

    public function getLabel()
    {
        $label    = "";
        $required = $this->getKeyRelation()->first();
        if ($required)
        {
            $label = $required->label;
        }
        return $label;
    }
}
