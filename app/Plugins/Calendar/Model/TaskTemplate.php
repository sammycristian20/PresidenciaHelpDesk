<?php

namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class TaskTemplate extends Model
{
    protected $table = 'task_templates';

    protected $fillable = ['name','description','category_id'];

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'category_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function templateTasks()
    {
        return $this->hasMany(TemplateTask::class, 'template_id')->orderBy('order');
    }

}
