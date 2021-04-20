<?php namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class CalendarAlerts extends Model
{

    protected $table = 'calendar_tasks_alerts';

    protected $fillable = ['id', 'task_id', 'send_alerts', 'repeat_alerts', 'created_at', 'updated_at'];

    public function task()
    {
        $related = "App\Plugins\Calendar\Model\Calendar";
        $foreignKey = "task_id";
        return $this->belongsTo($related, $foreignKey);
    }

    public function user()
    {
        $related = "App\User";
        $foreignKey = "user_id";
        return $this->belongsTo($related, $foreignKey);
    }
}
