<?php

namespace App\Model\helpdesk\TicketRecur;

use Illuminate\Database\Eloquent\Model;

class Recur extends Model
{

    protected $table    = 'recurs';
    protected $fillable = ['interval', 'delivery_on', 'start_date', 'end_date', 'last_execution', 'user_id', 'type', 'name', 'execution_time'];
    // protected $dates    = ['start_date', 'end_date', 'last_execution'];

    public function content()
    {
        return $this->hasMany('App\Model\helpdesk\TicketRecur\RecureContent');
    }
    public function delete()
    {
        $this->content()->delete();
        parent::delete();
    }

    public function getDeliveryOnAttribute($value)
    {
        return strtolower($value);
    }

    public function save(array $options = array())
    {
        $changed = $this->isDirty() ? $this->getDirty() : false;
        if ($changed && $this->id && !array_key_exists('last_execution', $changed)) $this->last_execution = null;
        // before save code
        parent::save($options);
        // Do stuff here
    }
}
