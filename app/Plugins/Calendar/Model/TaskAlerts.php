<?php 
namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class TaskAlerts extends Model
{

    protected $table = 'tasks_alerts';

    protected $fillable = ['id', 'task_id', 'repeat_alerts'];

    public function task()
    {
        $related = "App\Plugins\Calendar\Model\Task";
        $foreignKey = "task_id";
        return $this->belongsTo($related, $foreignKey);
    }

}
