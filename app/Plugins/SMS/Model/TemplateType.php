<?php namespace App\Plugins\SMS\Model;

use Illuminate\Database\Eloquent\Model;

class TemplateType extends Model
{
    protected $table = 'sms_template_types';
    protected $fillable = ['id', 'type', 'description', 'body', 'event_type', 'set_id', 'template_category'];
}
