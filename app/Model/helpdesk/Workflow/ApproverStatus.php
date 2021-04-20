<?php

namespace App\Model\helpdesk\Workflow;

use Illuminate\Database\Eloquent\Model;
use App\Model\helpdesk\Workflow\ApprovalLevelStatus;

class ApproverStatus extends Model
{
    protected $table = 'approver_statuses';

    protected $fillable = ['approver_id', 'approver_type','approval_level_status_id','status','hash','comment'];

    public function approvalLevelStatus()
    {
      return $this->belongsTo(ApprovalLevelStatus::class);
    }
}
