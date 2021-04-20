<?php namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class CalendarSettings extends Model {

	protected $table = 'calendar_settings';

	protected $fillable = ['id', 'send_reminder', 'email_reminder', 'sms_reminder', 'create_due_event', 'updated_at', 'created_at'];
}
