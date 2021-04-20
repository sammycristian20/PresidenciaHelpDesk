<?php

namespace App\Model\Custom;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $table = "forms";
    
    protected $fillable = ['form','json'];
    
    public function getKeyRelation()
    {
        return $this->belongsTo('App\Model\Custom\Required', 'key', 'field');
    }
}
