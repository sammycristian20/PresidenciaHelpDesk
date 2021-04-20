<?php
namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class TaskCategory extends Model{
    protected $fillable = ['id','name','project_id'];
    protected $table = 'task_categories';

   public function tasks()
   {
       return $this->hasMany('App\Plugins\Calendar\Model\Task', 'task_category_id');
   }

   public function templates()
   {
       return $this->hasMany('App\Plugins\Calendar\Model\TaskTemplate');
   }

   public function project()
    {
        return $this->belongsTo('App\Plugins\Calendar\Model\Project');
    }

}
