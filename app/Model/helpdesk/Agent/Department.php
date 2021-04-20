<?php

namespace App\Model\helpdesk\Agent;

use App\BaseModel;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;

class Department extends BaseModel
{
    protected $table = 'department';
    protected $hidden = ['pivot'];
    protected $fillable = [
        'name', 'type', 'sla', 'manager', 'ticket_assignment', 'outgoing_email',
        'template_set', 'auto_ticket_response', 'auto_message_response', 'en_auto_assign',
        'auto_response_email', 'recipient', 'group_access', 'department_sign', 'business_hour', 'nodes',
    ];

    protected $appends = ['form_identifier'];

    /**
     * This identifier will be used at frontend to know if it is a form_field, form field option, help topic option, department option or label
     * @return string
     */
    public function getFormIdentifierAttribute()
    {
        return "department_".$this->id;
    }

    public function assignAgent()
    {
        $related = "App\Model\helpdesk\Agent\DepartmentAssignAgents";
        return $this->hasMany($related, 'department_id');
    }

    public function businessHour()
    {
        $related = 'App\Model\helpdesk\Manage\Sla\BusinessHours';
        return $this->belongsTo($related, 'business_hour');
    }
    public function ticket()
    {
        return $this->hasMany('App\Model\helpdesk\Ticket\Tickets', 'dept_id');
    }

    public function helptopic()
    {
        return $this->hasMany('App\Model\helpdesk\Manage\Help_topic', 'department');
    }

    public function managerModel()
    {
        return $this->belongsTo('App\User', 'manager');
    }

    public function delete()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->transferExistingDepartmentAgentsToDefaultDepartment();
        $this->ticket()->update(['dept_id' => null]);
        parent::delete();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return true;
    }

    public function thread()
    {
        $related = 'App\Model\helpdesk\Ticket\Ticket_Thread';
        $through = 'App\Model\helpdesk\Ticket\Tickets';
        return $this->hasManyThrough($related, $through, 'dept_id', 'ticket_id', 'id');
    }

    public function responses()
    {
        return $this->thread()->where('poster', 'support')->where('is_internal', 0)->count();
    }

    /**
     * relationship for canned Responses
     */
    public function cannedResponses()
    {
        return $this->belongsToMany('App\Model\helpdesk\Agent_panel\Canned', 'department_canned_resposne', 'dept_id', 'canned_id')->withTimestamps();
    }

    public function avgResponseTime()
    {
        return $this->thread()->where('poster', 'support')->where('is_internal', 0)->avg('response_time');
    }

    public function totalResponseTime()
    {
        return $this->thread()->where('poster', 'support')->where('is_internal', 0)->sum('response_time');
    }
    /**
     * This relationship for users who belongs in this department as a manager
     *
     */

    public function managers()
    {

        return $this->belongsToMany('App\User', 'department_assign_manager', 'department_id', 'manager_id')->where([
            ['is_delete', 0],
            ['active', 1]
        ]);
    }
    /**
     * Relation with ticket filter share
     */
    public function ticketFilterShares()
    {
        return $this->morphToMany(\App\Model\helpdesk\Ticket\TicketFilter::class, 'ticket_filter_shareable');
    }

    //gives an array of form fields with same category id
    public function nodes(){
        // return $this->hasMany('App\Model\helpdesk\Form\FormField','category_id','id');
        return $this->morphMany('App\Model\helpdesk\Form\FormField', 'category');
    }


    /**
     * Relation with template set
     */
    public function templateSet()
    {
        return $this->hasMany(\App\Model\Common\TemplateSet::class, 'department_id');
    }

    /**
     * reference to form group
     */
    public function formGroups()
    {
      // where will sort
      return $this->belongsToMany('App\Model\helpdesk\Form\FormGroup','department_form_group')
        ->withPivot('sort_order','id');
    }

    /** 
     * method to transfer existing department agents to default Department, if they belong to only one department, if they are not part of  
     * default department
     * @return null
     */
    private function transferExistingDepartmentAgentsToDefaultDepartment()
    { 
        $defaultDepartmentId = defaultDepartmentId();
        $departmentAssignAgents = new DepartmentAssignAgents();
        $agents = $departmentAssignAgents->where('department_id', $this->id)->get();
        $this->assignAgent()->delete();
        if ($agents->isNotEmpty()) {
            foreach ($agents as $agent) {
                // after deleting the department and agent belongs to 0 department then transter him/her to default department
                if ($departmentAssignAgents->where('agent_id', $agent->agent_id)->count() == 0) {
                    $departmentAssignAgents->updateOrCreate(
                        ['department_id' => $defaultDepartmentId, 'agent_id' => $agent->agent_id],
                        ['department_id' => $defaultDepartmentId, 'agent_id' => $agent->agent_id]
                    );
                }
            }
        }
    }
}