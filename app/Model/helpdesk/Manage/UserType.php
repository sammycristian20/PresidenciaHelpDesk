<?php

namespace App\Model\helpdesk\Manage;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{

    /* The database table used by the model */
    protected $table = 'user_types';

    public $timestamps = false;

    protected $fillable = ['name', 'key'];

    /**
     * Relation with ticket filter share
     */
    public function approvalLevels()
    {
        return $this->morphToMany(\App\Model\helpdesk\Workflow\ApprovalLevel::class, 'approval_level_approver');
    }
}
