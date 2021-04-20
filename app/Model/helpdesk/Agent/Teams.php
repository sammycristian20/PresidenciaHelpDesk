<?php

namespace App\Model\helpdesk\Agent;

use App\BaseModel;

class Teams extends BaseModel
{
    protected $table = 'teams';
    protected $fillable = [
        'name', 'status', 'team_lead', 'assign_alert', 'admin_notes',
    ];

    public function ticket(){
        return $this->hasMany('App\Model\helpdesk\Ticket\Tickets','team_id');
    }

    public function delete() {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->ticket()->update(['team_id'=>null]);
        parent::delete();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function thread(){
        $related = 'App\Model\helpdesk\Ticket\Ticket_Thread';
        $through = 'App\Model\helpdesk\Ticket\Tickets';
        return $this->hasManyThrough($related, $through,'team_id','ticket_id','id');
    }

    public function responses(){
        return $this->thread()->where('poster','support')->where('is_internal',0)->count();
    }

    public function avgResponseTime(){
        return $this->thread()->where('poster','support')->where('is_internal',0)->avg('response_time');
    }

    public function totalResponseTime(){
        return $this->thread()->where('poster','support')->where('is_internal',0)->sum('response_time');
    }

    public function agents() {
        $related = 'App\Model\helpdesk\Agent\Assign_team_agent';
        return $this->hasMany($related, 'team_id')->whereNotNull('agent_id');
    }

    /**
     * gets the team lead
     */
    public function lead(){
        return $this->hasOne('App\User','id','team_lead')->where([
            ['is_delete', 0],
            ['active', 1]
        ]);
    }

}
