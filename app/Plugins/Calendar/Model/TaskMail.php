<?php


namespace App\Plugins\Calendar\Model;


use Illuminate\Database\Eloquent\Model;

class TaskMail extends Model
{
    protected $table = 'task_mails';

    protected $fillable = ['to','content','subject','processed'];
}