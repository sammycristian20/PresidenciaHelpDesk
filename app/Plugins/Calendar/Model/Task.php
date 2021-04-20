<?php

namespace App\Plugins\Calendar\Model;

use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\Calendar\Activity\Models\Activity;
use App\Plugins\Calendar\Activity\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use App\Model\helpdesk\Settings\System;
use App\User;

class Task extends Model
{
    use LogsActivity;

    protected $table = 'tasks';

    protected static $logName = 'task';

    protected static $logAttributes = ['task_name', 'task_start_date',
        'task_end_date', 'status', 'is_private', 'ticket_id', 'task_category_id'
    ];

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    protected static $recordEvents = ['created','updated'];

    protected $fillable = [
        'id', 'task_name', 'task_description', 'task_start_date',
        'task_end_date', 'status', 'is_private', 'ticket_id',
        'parent_id', 'is_complete', 'created_by', 'due_alert',
        'task_category_id','due_alert_text','alert_repeat_text',
        'template_task_due','task_template_id','order'
    ];

    protected $appends = ['url','assigned_agents'];

    public function assignedTo()
    {
        return $this->hasMany('App\Plugins\Calendar\Model\TaskAssignees', 'task_id');
    }

    public function taskCategory()
    {
        return $this->belongsTo('App\Plugins\Calendar\Model\TaskCategory', 'task_category_id');
    }

    public function taskTemplate()
    {
        return $this->belongsTo(TaskTemplate::class);
    }

    public function Alerts()
    {
        return $this->hasOne('App\Plugins\Calendar\Model\TaskAlerts', 'task_id');
    }

    public function getUrlAttribute()
    {
        return url('tasks/task/' . $this->id);
    }

    public function getTaskStartDateAttribute($value)
    {
        if (!$value) {
            return null;
        } 
        return \Carbon\Carbon::parse($value, 'UTC')->setTimezone(System::first()->time_zone);
    }

    public function getTaskEndDateAttribute($value)
    {
        if (!$value) {
            return null;
        } 
        return \Carbon\Carbon::parse($value, 'UTC')->setTimezone(System::first()->time_zone);
    }

    public function getDueAlertAttribute($value)
    {
        return \Carbon\Carbon::parse($value, 'UTC')->setTimezone(System::first()->time_zone);
    }

    public function ticket()
    {
        return $this->belongsTo(Tickets::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getAssignedAgentsAttribute()
    {
        $assignees = TaskAssignees::where('task_id', $this->id)->get(['user_id']);

        $assignedAgents = $assignees->map(function ($item, $key) {
            $user = User::where('id', $item->user_id)->first(['id','first_name','last_name']);
            return ['id' => $user->id, 'name' => $user->meta_name];
        });
        return $assignedAgents;
    }

    public function getCreatedByAttribute($value)
    {
        return User::where('id', $value)->first(['id','first_name','last_name']);
    }

    public function getTemplateTaskDueAttribute($value)
    {
        return json_decode($value, true);
    }

    public function activityLog()
    {
        return $this->hasMany(Activity::class, 'source_id')
            ->where('source_type', 'App\Plugins\Calendar\Model\Task');
    }
}
