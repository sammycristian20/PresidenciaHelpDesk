<?php

namespace App\Model\helpdesk\Workflow;

use App\Model\helpdesk\Workflow\ApproverStatus;
use Illuminate\Database\Eloquent\Model;
use App\Model\helpdesk\Manage\UserType;
use App\Model\helpdesk\Workflow\ApprovalWorkflowTicket;
use App\User;
use App\Traits\Observable;

class ApprovalLevelStatus extends Model
{
    use Observable;

    protected $table = 'approval_level_statuses';

    protected $fillable = ['approval_level_id', 'approval_workflow_ticket_id', 'name', 'match',
        'order','is_active','status'];

    /**
     * Relation with approval level
     */
    public function approvalLevel()
    {
        return $this->belongsTo(ApprovalLevel::class);
    }

    public function approvalWorkflow()
    {
        return $this->belongsTo(ApprovalWorkflowTicket::class, 'approval_workflow_ticket_id');
    }

    /**
     * relation with Approver Statuses
     */
    public function approverStatuses()
    {
      return $this->hasMany(ApproverStatus::class);
    }

    /**
     * Relation with user
     */
    public function approveUsers()
    {
        return $this->morphedByMany(User::class, 'approver','approver_statuses')
          ->withPivot('hash', 'status')->select('users.id','first_name','last_name','user_name','email');
    }

    /**
     * Relation with user types
     */
    public function approveUserTypes()
    {
        return $this->morphedByMany(UserType::class, 'approver','approver_statuses')->withPivot('hash', 'status');
    }

    /**
     * method to delete approverStatus relation
     */
    public function beforeDelete($model)
    {
      foreach ($model->approverStatuses()->get() as $approverStatus) {
          $approverStatus->delete();
      }
    }
}
