<?php

namespace App\Model\helpdesk\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentTypeRelation extends Model
{
    protected $table = 'agent_type_relations';
    protected $fillable=[
        'type_id','agent_id',
    ];
    
    public function type(){
        return $this->belongsTo('App\Model\helpdesk\Manage\Tickettype','type_id');
    }
}
