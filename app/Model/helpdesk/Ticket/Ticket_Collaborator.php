<?php

namespace App\Model\helpdesk\Ticket;

use App\BaseModel;

class Ticket_Collaborator extends BaseModel
{
    protected $table = 'ticket_collaborator';
    protected $fillable = [
                            'id', 'isactive', 'ticket_id', 'user_id', 'role', 'updated_at', 'created_at',
                            ];
    
    public function user(){
        return $this->hasMany('App\User', 'user_id');
    }
    
    public function userBelongs(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
