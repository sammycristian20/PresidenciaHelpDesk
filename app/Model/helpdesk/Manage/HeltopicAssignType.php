<?php

namespace App\Model\helpdesk\Manage;

use App\BaseModel;

class HeltopicAssignType extends BaseModel
{

    protected $table    = 'helptopic_assign_type';
    protected $fillable = [
        'id', 'helptopic_id',  'type_id','created_at','updated_at'
    ];

   

}
