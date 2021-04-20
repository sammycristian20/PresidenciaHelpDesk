<?php

namespace App\Model\helpdesk\Workflow;

use App\Model\helpdesk\Manage\UserType;
use App\Model\helpdesk\Workflow\ApprovalWorkflow;
use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\helpdesk\Workflow\ApprovalLevelStatus;
use App\Traits\Observable;

class ApprovalLevel extends Model
{
    use Observable;
    
    public $timestamps = false;

    protected $fillable = ['name', 'match', 'approval_workflow_id', 'order'];

    /**
     * Relation with approval workflow
     */
    public function approvalWorkflows()
    {
        return $this->belongsTo(ApprovalWorkflow::class);
    }

    /**
     * Relation with approval workflow
     */
    public function approvalLevelStatus()
    {
        return $this->hasOne(ApprovalLevelStatus::class);
    }
    /**
     * Relation with user
     */
    public function approveUsers()
    {
        return $this->morphedByMany(User::class, 'approval_level_approver');
    }

    /**
     * Relation with user types
     */
    public function approveUserTypes()
    {
        return $this->morphedByMany(UserType::class, 'approval_level_approver');
    }

     /**
     * delete approval workflow level
     * @param $model
     * @return 
     */
    public function beforeDelete($model)
    {
      // Remove approver users
      $model->approveUsers()->detach();

      // Remove approver user types
      $model->approveUserTypes()->detach();     
    }
}
