<?php

namespace App\Model\helpdesk\Theme;

use App\BaseModel;

class Widgets extends BaseModel
{
    protected $table = 'widgets';
    protected $fillable = ['name', 'value', 'title', 'created_at', 'updated_at'];

    /**
     * Mutator to save value as null if $value is an empty string
     */
    public function setValueAttribute($value)
    {
    	$this->attributes['value'] = $this->returnValueAfterEmptyCheck($value);
    }

    /**
     * Mutator to save title as null if $value is an empty string
     */
    public function setTitleAttribute($value)
    {
    	$this->attributes['title'] = $this->returnValueAfterEmptyCheck($value);
    }

    /**
     * Function checks if $value is an empty string or not and returns
     * string or null
     * $value will be empty if it has values like
     * null,'', string with whitespaces eg '    '
     *
     * @param   string  $value
     * @return  mixed           null if $value is empty string else string value
     *                          of $value
     */
    private function returnValueAfterEmptyCheck($value)
    {
    	return (trim($value)) ? $value : null;
    }
}
