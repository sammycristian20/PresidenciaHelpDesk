<?php

namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{

    protected $table = 'tasks';

    protected $fillable = ['id', 'start', 'end', 'name', 'description', 'all_day', 'due', 'ticket_id', 'is_private', 'p_id', 'created_at', 'updated_at'];

    public function taskAssignees()
    {
        return $this->hasMany('App\Plugins\Calendar\Model\CalendarAssignees', 'task_id');
    }

    public function taskThreads()
    {
        return $this->hasMany('App\Plugins\Calendar\Model\CalendarThread', 'task_id');
    }

    public function taskList()
    {
        return $this->belongsTo('App\Plugins\Calendar\Model\TaskList','task_list_id');
    }

    public function taskAlerts()
    {
        return $this->hasOne('App\Plugins\Calendar\Model\CalendarAlerts', 'task_id');
    }

    public function delete()
    {
    	$this->taskAssignees()->delete();
    	$this->taskThreads()->delete();
        $this->taskAlerts()->delete();
        parent::delete();
    }
}
