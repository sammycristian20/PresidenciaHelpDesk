<?php

namespace App\Model\helpdesk\Workflow;

use App\Model\helpdesk\Workflow\ApprovalLevel;
use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\helpdesk\Ticket\Ticket_Status as Status;
use App\Traits\Observable;

class ApprovalWorkflow extends Model
{
    use Observable;

    protected $fillable = ['name', 'user_id', 'action_on_approve','action_on_deny', 'type'];

    /**
     * Relation with user
     */
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation with approval level
     */
    public function approvalLevels()
    {
        return $this->hasMany(ApprovalLevel::class);
    }

    /**
     * sets default value of `action_on_approve` when not selected
     */
    public function setActionOnApproveAttribute($value)
    {
      $value = !$value ? 1 : $value;
      $this->attributes['action_on_approve'] = $value;
    }

    /**
     * sets default value of `action_on_deny` when not selected
     */
    public function setActionOnDenyAttribute($value)
    {
      $value = !$value ? 3 : $value;
      $this->attributes['action_on_deny'] = $value;
    }

    /**
     * relation for getting corresponding action status on approve
     */
    public function actionOnApprove()
    {
      return $this->belongsTo('App\Model\helpdesk\Ticket\Ticket_Status','action_on_approve')
        ->select('id','name');
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
     * relation for getting corresponding action status on deny
     */
    public function actionOnDeny()
    {
      return $this->belongsTo('App\Model\helpdesk\Ticket\Ticket_Status','action_on_deny')
        ->select('id','name');
    }

    /**
     * delete approval workflow
     * @param $model
     * @return 
     */
    public function beforeDelete($model)
    {
      $approvalLevels = $model->approvalLevels()->get();

      foreach ($approvalLevels as $level) {
        // Remove approval level
        $level->delete();
      }      
    }
}
