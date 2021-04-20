<?php

namespace App\Model\helpdesk\Ticket;

use App\Model\helpdesk\Manage\Sla\BusinessHours;
use App\Model\helpdesk\Manage\Sla\SlaApproachEscalate;
use App\Model\helpdesk\Manage\Sla\SlaViolatedEscalate;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;
use Lang;
use UnexpectedValueException;

class TicketSla extends Model
{
    use Observable;

    protected $table = 'ticket_slas';

    protected $fillable = [

        /**
         * name of the workflow
         */
        'name',

        /**
         * If status is active or not
         */
        'status',

        /**
         * Order of the workflow
         */
        'order',

        /**
         * All or any action match triggers workflow
         */
        'matcher',

        /**
         * if default SLA
         */
        'is_default',

        /**
         * internal notes for description purpose
         */
        'internal_notes',
    ];

    /**
     * using polymorphic relation for binding
     * @return array      array of rules
     */
    public function rules()
    {
        return $this->morphMany('App\Model\helpdesk\Ticket\TicketRule', 'reference');
    }

    //deletes child data as soon as workflow is deleted
    public function beforeDelete($model)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');
        set_time_limit(0);

        foreach ($this->rules as $rule) {
            $rule->delete();
        }

        // delete all reminders
        foreach ($this->approachingReminders as $reminder) {
            $reminder->delete();
        }

        foreach ($this->violatedReminders as $reminder) {
            $reminder->delete();
        }

    }

    /**
     * Performs actions required afer SLA is deleted
     * for eg. moving tickets in that SLA to default SLA
     * @param $model
     */
    public function afterDelete($model)
    {
        $defaultSlaId = TicketSla::where("is_default", 1)->value("id");

        $tickets = Tickets::where("sla", $model->id)->select("id")->get();

        foreach ($tickets as $ticket) {
            $ticket->sla = $defaultSlaId;
            $ticket->save();
        }
    }

    public function approachingReminders()
    {
        return $this->hasMany(SlaApproachEscalate::class, 'sla_plan');
    }

    public function violatedReminders()
    {
        return $this->hasMany(SlaViolatedEscalate::class, 'sla_plan');
    }

    public function slaMeta()
    {
        return $this->hasMany(TicketSlaMeta::class, 'ticket_sla_id', 'id');
    }

    public function afterModelActivity($model)
    {
        // make order of default SLA as last
        $maxOrder = (int)TicketSla::where('is_default', 0)->orderBy('order', 'desc')->value('order');

        $defaultSla = TicketSla::where('is_default', 1)->first();

        if ($defaultSla->order != $maxOrder + 1){
            $defaultSla->order = $maxOrder + 1;
            $defaultSla->save();
        }
    }

    public function setIsDefaultAttribute($value)
    {
        // if default is true and there already is a default present,
        // it should make that default as false
        if($value){
            TicketSla::where('id', '!=', $this->id)->where('is_default', 1)->update(['is_default'=>0]);
        }
        // if default is false, and no other default is present it should throw an exception
        else {
            $isAnotherDefaultPresent = TicketSla::where('id', '!=', $this->id)->where('is_default', 1)->count();
            if(!$isAnotherDefaultPresent){
                throw new UnexpectedValueException(Lang::get("lang.default_sla_cannot_be_deleted"));
            }
        }

        $this->attributes['is_default'] = $value;
    }

    public function setStatusAttribute($value)
    {
        // if default is true and there already is a default present,
        // it should make that default as false
        if(!$value && $this->is_default){
            throw new UnexpectedValueException(Lang::get("lang.default_sla_cannot_be_deactivated"));
        }

        $this->attributes['status'] = $value;
    }

    /**
     * Gives Sla meta data for a ticket
     * @param Tickets $ticket
     * @return TicketSlaMeta|null
     */
    public static function getSlaMetaDataByTicket(Tickets $ticket)
    {
        $slaPlan = $ticket->slaPlan;
        if($slaPlan) {
           return $slaPlan->slaMeta()->where('priority_id', $ticket->priority_id)->first();
        }
    }
}
