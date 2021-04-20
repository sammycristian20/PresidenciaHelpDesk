<?php namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class CalendarThread extends Model
{

    protected $table = 'tasks_threads';

    protected $fillable = ['id', 'system_note', 'poster', 'created_at', 'updated_at'];

}

