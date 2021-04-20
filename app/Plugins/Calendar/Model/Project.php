<?php
namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class Project extends Model{
    protected $fillable = ['id','name'];
    protected $table = 'projects';

    public function categories()
    {
        return $this->hasMany(TaskCategory::class,'project_id');
    }


    public function tasks()
    {
        return $this->hasManyThrough(Task::class, TaskCategory::class, 'project_id', 'task_category_id', 'id', 'id');
    }
}
