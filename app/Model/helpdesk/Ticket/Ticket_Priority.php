<?php

namespace App\Model\helpdesk\Ticket;

use App\BaseModel;
use App\Model\helpdesk\Manage\Sla\BusinessHours;
use App\Traits\Observable;

class Ticket_Priority extends BaseModel
{
    use Observable;

	protected $primaryKey = 'priority_id';

	public $timestamps = false;

    protected $table = 'ticket_priority';

    protected $fillable = [
        'priority_id', 'priority', 'status','user_priority_status','priority_desc', 'priority_color', 'priority_urgency', 'is_default', 'ispublic','created_at', 'updated_at',
    ];

    public function afterCreate($model)
    {
        // create meta data for all SLA's for current priority
        // get all SLA's, loop over them and create meta data for the priority
        $slas = TicketSla::select('id')->get();
        $defaultBusinessHourId = BusinessHours::where('is_default', 1)->select('id')->value('id');

        foreach ($slas as $sla) {
            $sla->slaMeta()->create(["priority_id"=> $model->priority_id, 'business_hour_id'=> $defaultBusinessHourId]);
        }
    }

    public function beforeDelete($model)
    {
        // deleting priority's SLA meta data
        TicketSlaMeta::where('priority_id', $model->priority_id)->delete();
    }
}
