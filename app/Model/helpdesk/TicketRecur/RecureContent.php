<?php

namespace App\Model\helpdesk\TicketRecur;

use Illuminate\Database\Eloquent\Model;

class RecureContent extends Model
{
    protected $table = 'recure_contents';
    protected $fillable = ['recur_id','option','value'];

    public function save(array $options = array())
    {
        $changed = $this->isDirty() ? $this->getDirty() : false;
        if ($changed) session(['lastExecNull' => true]);
        // before save code
        parent::save($options);
        // Do stuff here
    }

    public function setValueAttribute($value)
    {
      //so that array can be handled just like string
      $this->attributes['value'] = json_encode($value);
    }

    public function getValueAttribute($value)
    {
      //so that array can be handled just like string
      return json_decode($value);
      // return $value;
    }
}
