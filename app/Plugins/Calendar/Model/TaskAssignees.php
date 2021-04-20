<?php 
namespace App\Plugins\Calendar\Model;

use App\Plugins\Calendar\Activity\Models\Activity;
use App\Plugins\Calendar\Activity\Traits\LogsActivity;
use App\Plugins\Calendar\Model\Task;
use Illuminate\Database\Eloquent\Model;

class TaskAssignees extends Model
{
    use LogsActivity;

    protected static $logName = 'task';

    protected static $submitEmptyLogs = false;

    protected $table = 'task_assignees';

    protected static $logAttributes = ['user_id', 'task_id'];

    protected static $recordEvents = ['created','deleted'];

    protected $fillable = ['id', 'task_id', 'user_id', 'team_id'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function activityLog()
    {
        return $this->hasMany(Activity::class, 'source_id')
            ->where('source_type', 'App\Plugins\Calendar\Model\TaskAssignees');
    }
}
