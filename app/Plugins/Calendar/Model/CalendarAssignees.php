<?php namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class CalendarAssignees extends Model
{

    protected $table = 'calendar_task_assignees';

    protected $fillable = ['id', 'task_id', 'user_id', 'team_id', 'created_at', 'updated_at'];

    public function team()
    {
        $related = "App\Model\helpdesk\Agent\Teams";
        $foreignKey = "team_id";
        return $this->belongsTo($related, $foreignKey);
    }

    public function user()
    {
        $related = "App\User";
        $foreignKey = "user_id";
        return $this->belongsTo($related, $foreignKey);
    }

    public function task()
    {
        $related = "App\Plugins\Calendar\Model\Calendar";
        $foreignKey = "task_id";
        return $this->belongsTo($related, $foreignKey);
    }
}
