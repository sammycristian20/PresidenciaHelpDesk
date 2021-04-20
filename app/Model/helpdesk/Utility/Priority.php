<?php

namespace App\Model\helpdesk\Utility;

use App\BaseModel;

class Priority extends BaseModel
{
    public $timestamps = false;
    protected $table = 'priority';
    protected $fillable = [
        'id', 'name',
    ];
}
