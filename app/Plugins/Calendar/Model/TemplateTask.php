<?php

namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class TemplateTask extends Model
{
    protected $table = 'template_tasks';

    protected $fillable = ['template_id','name','assignees','end','end_unit','order','assign_task_to_ticket_agent','category'];

    public function template()
    {
        return $this->belongsTo(TaskTemplate::class);
    }

    public function getAssigneesAttribute($value)
    {
        return $value ?explode(',', $value) : [];
    }
}
