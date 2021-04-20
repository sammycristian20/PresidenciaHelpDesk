<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAdditionalInfo extends Model
{

    protected $table    = "user_additional_infos";
    protected $fillable = ["owner", "service", "key", "value",];

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
