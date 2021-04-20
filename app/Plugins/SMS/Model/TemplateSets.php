<?php namespace App\Plugins\SMS\Model;

use Illuminate\Database\Eloquent\Model;

class TemplateSets extends Model
{
    protected $table = 'sms_template_sets';
    protected $fillable = ['id', 'name', 'status', 'is_default', 'template_language'];
}
