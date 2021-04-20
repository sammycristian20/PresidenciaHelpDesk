<?php namespace App\Plugins\LimeSurvey\Model;

use Illuminate\Database\Eloquent\Model;

class LimeSurvey extends Model
{

    protected $table = 'lime_survey';

    protected $fillable = ['name', 'survey_link', 'created_at', 'updated_at'];

}
