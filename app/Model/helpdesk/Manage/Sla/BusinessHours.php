<?php

namespace App\Model\helpdesk\Manage\Sla;

use App\BaseModel;
use App\Model\helpdesk\Ticket\TicketSlaMeta;
use App\Traits\Observable;

class BusinessHours extends BaseModel
{
    use Observable;

    protected $table = 'business_hours';

    protected $fillable = ['name', 'description', 'status', 'timezone', 'is_default'];

    public function schedule(){
        $related = 'App\Model\helpdesk\Manage\Sla\BusinessHoursSchedules';
        $foreignKey = 'business_hours_id';
        return $this->hasMany($related, $foreignKey);
    }

    public function holiday(){
        $related = 'App\Model\helpdesk\Manage\Sla\BusinessHoliday';
        $foreignKey = 'business_hours_id';
        return $this->hasMany($related, $foreignKey);
    }

    public function beforeDelete($model)
    {
        $slaMetas = TicketSlaMeta::where("business_hour_id", $model->id)->get();

        if(!$slaMetas->count()){
            return;
        }

        $defaultBusinessHourId = BusinessHours::where('is_default', 1)->value("id");

        foreach ($slaMetas as $slaMeta) {
            $slaMeta->business_hour_id = $defaultBusinessHourId;
            $slaMeta->save();
        }
    }
}
