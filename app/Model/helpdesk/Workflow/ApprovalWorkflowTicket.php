<?php

namespace App\Model\helpdesk\Workflow;

use Illuminate\Database\Eloquent\Model;
use App\Model\helpdesk\Workflow\ApprovalWorkflow;
use App\Model\helpdesk\Workflow\ApprovalLevelStatus;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Status as Status;
use App\Traits\Observable;

class ApprovalWorkflowTicket extends Model
{
    use Observable;

    protected $table = 'approval_workflow_tickets';

    protected $fillable = ['approval_workflow_id', 'ticket_id', 'status', 'name', 'user_id', 'action_on_approve','action_on_deny', 'ticket_status_id'];

    /**
     * Relation with approval level
     */
    public function approvalWorkflow()
    {
        return $this->belongsTo(ApprovalWorkflow::class);
    }

    /**
     * Relation with approval level
     */
    public function approvalLevels()
    {
        return $this->hasMany(ApprovalLevelStatus::class);
    }

    public function ticket(){
      return $this->belongsTo(Tickets::class,'ticket_id');
    }

    //action on approve and action on deny should check
    public function getActionOnApproveAttribute($value)
    {
      //if asked status does not exist, if will give open status
      if(!Status::where('id', $value)->count()){
        return 1;
      }
      return $value;
    }

    public function getActionOnDenyAttribute($value)
    {
      //if asked status does not exist, if will give open status
      if(!Status::where('id', $value)->count()){
        return 3;
      }
      return $value;
    }

    /** 
     * method to delete approvalLevels relation
     */
    public function beforeDelete($model)
    {
      foreach ($model->approvalLevels()->get() as $approverLevel)
      {
        $approverLevel->delete();
      }
    }
}
