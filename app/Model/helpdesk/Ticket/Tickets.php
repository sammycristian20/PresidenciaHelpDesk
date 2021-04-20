<?php

namespace App\Model\helpdesk\Ticket;

use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use Illuminate\Database\Eloquent\Model;
use Carbon;
use App\Model\helpdesk\Settings\Ticket as TicketSetting;
use Cache;
use App\Model\helpdesk\Ticket\Ticket_Priority as TicketPriority;
use App\Model\helpdesk\Ticket\Ticket_source as TicketSource;
use App\Model\helpdesk\Settings\System as SettingSystem;
use App\Model\helpdesk\Manage\Help_topic as HelpTopic;
use Illuminate\Support\Facades\Crypt;
use App\User;

class Tickets extends Model
{
    protected $table    = 'tickets';

    protected $fillable = ['ticket_number', 'num_sequence', 'user_id', 'priority_id', 'type', 'sla', 'help_topic_id', 'max_open_ticket', 'captcha', 'status', 'lock_by', 'lock_at', 'source', 'isoverdue', 'reopened', 'isanswered', 'is_deleted', 'closed', 'is_transfer', 'transfer_at', 'reopened_at', 'closed_at', 'last_message_at', 'first_response_time','assigned_to', 'resolution_time', 'is_response_sla', 'is_resolution_sla', 'dept_id', 'duedate','domain_id', 'team_id','location_id','creator_id','response_due_by', 'is_manual_duedate','last_estd_duedate', 'average_response_time','resolution_due_by', 'parent_id', 'parent_ticket_id'];
    protected $dates    = ['duedate', 'closed_at','first_response_time','response_due_by', 'resolution_due_by'];

    public $notify      = true;
    public $system      = false;
    public $send        = true;
    public $event       = false;
    public $appends  = ['thread_count', 'collaborator_count', 'attachment_count', 'poster', 'encrypted_id'];

    public function attach()
    {
        return $this->hasManyThrough(
            'App\Model\helpdesk\Ticket\Ticket_attachments',
            'App\Model\helpdesk\Ticket\Ticket_Thread',
            'ticket_id',
            'thread_id'
        );
    }

    public function threadSelectedFields()
    {
        return $this->hasOne('App\Model\helpdesk\Ticket\Ticket_Thread', 'ticket_id')->addSelect(
            'ticket_id',
            \DB::raw('substring_index(group_concat(title order by id asc SEPARATOR "-||,||-") , "-||,||-", 1) as title'),
            \DB::raw('substring_index(group_concat(if(`is_internal` = 0, `poster`,null)ORDER By id desc) , ",", 1) as poster'),
            \DB::raw('CONVERT_TZ(max(updated_at), "+00:00", "'.getGMT().'") as updated_at2')
        )->where('is_internal', '=', 0)->groupBy('ticket_id');
    }

    public function thread()
    {
        return $this->hasMany('App\Model\helpdesk\Ticket\Ticket_Thread', 'ticket_id');
    }
    public function collaborator()
    {
        return $this->hasMany('App\Model\helpdesk\Ticket\Ticket_Collaborator', 'ticket_id');
    }
    public function helptopic()
    {
        $related    = 'App\Model\helpdesk\Manage\Help_topic';
        $foreignKey = 'help_topic_id';
        return $this->belongsTo($related, $foreignKey);
    }

    //first thread of the ticket
    //usually used for extracting subject of the ticket
    public function firstThread()
    {
        return $this->hasOne('App\Model\helpdesk\Ticket\Ticket_Thread', 'ticket_id')->where('is_internal', '==', 1)->orderBy('id', 'ASC');
    }

    //last thead of the ticket
    //usually used for extracting last replier's name
    public function lastThread()
    {

        //need get the last thread for which is_internal is  not 1(if someone replies is_internal is 1), to
        //avoid is_intenal to include as a reply
        return $this->hasOne('App\Model\helpdesk\Ticket\Ticket_Thread', 'ticket_id')->where('is_internal', '!=', 1)
                    ->orderBy('id', 'DESC');
    }

    public function formdata()
    {
        //quering only non-empty fields
        return $this->hasMany('App\Model\helpdesk\Ticket\Ticket_Form_Data', 'ticket_id')->where('content', '!=', "");
    }

    public function extraFields()
    {
        $id                = $this->attributes['id'];
        $ticket_form_datas = \App\Model\helpdesk\Ticket\Ticket_Form_Data::where('ticket_id', '=', $id)->get();
        return $ticket_form_datas;
    }
    public function sources()
    {
        return $this->belongsTo('App\Model\helpdesk\Ticket\Ticket_source', 'source');
    }
    public function sourceCss()
    {
        $css    = "fa fa-comment";
        $source = $this->sources();
        if ($source->first()) {
            $css = $source->first()->css_class;
        }
        return $css;
    }
    public function filter()
    {
        $related = 'App\Model\helpdesk\Filters\Filter';
        return $this->hasMany($related, 'ticket_id');
    }

    public function setAssignedToAttribute($value)
    {
        if(!$value) {
            $this->attributes['assigned_to'] = null;
            return;
        }
        //update assigned to value if new agent belongs to ticket's department or has global access
        if($this->departmentMatchesWithAssigned($value, $this->dept_id)) {
            $this->attributes['assigned_to'] = $value;
            $this->attributes['team_id'] = null;
            return;
        }
        //retain old assigned_to if attribute is set and agent belongs to ticket's department or has global access
        if(array_key_exists('assigned_to', $this->attributes)) {
            $this->attributes['assigned_to'] = $this->departmentMatchesWithAssigned($this->attributes['assigned_to'], $this->dept_id);
        }
    }

    public function setTeamIdAttribute($value)
    {
        if ($value) {
            $this->attributes['assigned_to'] = null;
        }
        $this->attributes['team_id'] = $value;
    }

    public function getAssignedTo()
    {
        $agentid = $this->attributes['assigned_to'];
        if ($agentid) {
            $users = new \App\User();
            $user  = $users->where('id', $agentid)->first();
            if ($user) {
                return $user;
            }
        }
    }
    public function user()
    {
        $related    = "App\User";
        $foreignKey = "user_id";
        return $this->belongsTo($related, $foreignKey);
    }

    public function creator()
    {
        $related    = "App\User";
        $foreignKey = "creator_id";
        return $this->belongsTo($related, $foreignKey);
    }

    public function labels()
    {
        $related    = "App\Model\helpdesk\Filters\Filter";
        $foreignKey = "ticket_id";
        return $this->hasMany($related, $foreignKey)
        ->leftJoin('labels', 'filters.value', '=', 'labels.title')
        ->where('key', 'label');
    }

    public function tags()
    {
        $related    = "App\Model\helpdesk\Filters\Filter";
        $foreignKey = "ticket_id";
        return $this->hasMany($related, $foreignKey)
        ->leftJoin('tags', 'filters.value', '=', 'tags.name')
        ->where('key', 'tag');
    }

    public function assigned()
    {
        $related    = 'App\User';
        $foreignKey = 'assigned_to';
        return $this->belongsTo($related, $foreignKey);
    }
    public function assignedTeam()
    {
        $related    = 'App\Model\helpdesk\Agent\Teams';
        $foreignKey = 'team_id';
        return $this->belongsTo($related, $foreignKey)->withDefault();
    }

    /**
     * @depreciated because nameing convention is not correct
     */
    public function departments()
    {
        $related    = 'App\Model\helpdesk\Agent\Department';
        $foreignKey = 'dept_id';
        return $this->belongsTo($related, $foreignKey);
    }

    /**
     * gets the department details for which ticket is mapped in
     */
    public function department()
    {
        $related    = 'App\Model\helpdesk\Agent\Department';
        $foreignKey = 'dept_id';
        return $this->belongsTo($related, $foreignKey);
    }

    public function slaPlan()
    {
        return $this->belongsTo(TicketSla::class, 'sla');
    }

    public function statuses()
    {
        $related    = 'App\Model\helpdesk\Ticket\Ticket_Status';
        $foreignKey = 'status';
        return $this->belongsTo($related, $foreignKey);
    }
//    public function setIsansweredAttribute($value) {
//        $this->attributes['isanswered'] = $value;
//        if ($value == 1) {
//            $this->attributes['duedate'] = $this->getResolutionDue();
//        }else{
//             $this->attributes['duedate'] = $this->overdue();
//        }
//    }


    public function getResolutionDue()
    {
        $slaid = $this->attributes['sla'];
        if ($slaid) {
            $ticket_created = $this->created_at;
            $apply_sla      = new \App\Http\Controllers\SLA\ApplySla();
            $due            = $apply_sla->slaResolveDue($slaid, $ticket_created);
            return $due;
        }
    }

    public function priority()
    {
        $related    = 'App\Model\helpdesk\Ticket\Ticket_Priority';
        $foreignKey = 'priority_id';
        return $this->belongsTo($related, $foreignKey);
    }
    public function save(array $options = array())
    {
        $changed = $this->isDirty() ? $this->getDirty() : false;
        $id      = $this->id;
        $model   = $this->find($id);

        $save = parent::save($options);
        if ($this->notify) {
            $array = ['changes' => $changed, 'model' => $model, 'system' => $this->system, 'send_mail' => $this->send];
            \Event::dispatch('notification-saved', [$array]);
        }
        return $save;
    }
    public function halt()
    {
        $related = "App\Model\helpdesk\Ticket\Halt";
        $foreign = "ticket_id";
        return $this->hasOne($related, $foreign);
    }
    public function types()
    {
        $related    = 'App\Model\helpdesk\Manage\Tickettype';
        $foreignKey = 'type';
        return $this->belongsTo($related, $foreignKey);
    }

    public function timeTracks()
    {
        return $this->hasMany(\App\TimeTrack\Models\TimeTrack::class, 'ticket_id');
    }

    public function getThreadCountAttribute()
    {

        //this has to be fetched from ticket settings
        $settingsTicket = Cache::get('settings_ticket', function () {
            return TicketSetting::first();
        });

        $countInternalNotes = $settingsTicket->count_internal;

        $baseQueryForThreadCount = $this->thread()->where(function ($q) use ($countInternalNotes) {
            $q = $q->where('is_internal', '=', 0);

            if ($countInternalNotes) {
                $q = $q->orWhere('thread_type', 'note');
            }

            return $q;
        });

        return $baseQueryForThreadCount->count();
    }

    public function getAttachmentCountAttribute()
    {
        return $this->attach()->count();
    }
    public function collaboratorCountRelation()
    {
        return $this
               ->collaborator()
                ->whereHas('userBelongs', function ($q) {
                    $q->where('email', '!=', '')->whereNotNull('email');
                })

                ->selectRaw('ticket_id, count(*) as count')
                ->groupBy('ticket_id');
    }
    public function getCollaboratorCountAttribute()
    {
        $first = $this->collaboratorCountRelation->first();
        if ($first) {
            return $first->count;
        }
    }

    public function attachment()
    {
        $related = 'App\Model\helpdesk\Ticket\Ticket_attachments';
        $through = 'App\Model\helpdesk\Ticket\Ticket_Thread';
        return $this->hasManyThrough($related, $through, 'ticket_id', 'thread_id', 'id');
    }

    public function getPosterAttribute()
    {
        $thread = $this->thread()->where('is_internal', '=', 0)->get()->last();
        if ($thread) {
            return $thread->poster;
        }
        return '';
    }


    /**
     * checks if a ticket is due for today based on Agent's timezone
     * A TICKET WILL BE DUE TODAY IF ITS DUEDATE FALLS BETWEEN CURRENT TIME AND END OF DAY TIME IN AGENT'S TIMEZONE
     *
     * NOTE: this method works on the assumption that datetime provided to it must be in UTC
     * @param string $duedate           due date fo ticket in UTC
     * @param string $agentTimeZone     logged in agent's timezone
     * @return boolean                  true if due today else false
     */
    public static function isDueToday($duedate, $agentTimeZone)
    {
        if (!$duedate) {
            return false;
        }

        //converting due date from UTC to agent's timezone
        $dueDateInAgentTZ = changeTimezoneForDatetime($duedate, 'UTC', $agentTimeZone);

        //current time
        $currentTime = Carbon\Carbon::now($agentTimeZone);

        //current time
        $todayEOD = Carbon\Carbon::tomorrow($agentTimeZone);

        $ifDueToday = ($dueDateInAgentTZ > $currentTime && $dueDateInAgentTZ < $todayEOD) ? true : false;

        return $ifDueToday;
    }

    /**
     * compares duedate with current time and tells if it is overdue and unanswered
     * @param string $duedate           due date fo ticket in UTC
     * @return boolean                  true if given ticket is overdue else false
     */
    public static function isOverdue($duedate)
    {
        if (!$duedate) {
            return false;
        }

        $currentTime = Carbon\Carbon::now();

        //if current time is greater than duedate
        $isOverdue = ($currentTime > $duedate) ? true : false;
        return $isOverdue;
    }

    /**
     * returns rating object
     */
    public function ratings()
    {
        return $this->hasMany('App\Model\helpdesk\Ratings\RatingRef', 'ticket_id');
    }

    /**
     * Gets custom field values by passed ticket Id
     * @param integer $ticketId     Id of the ticket for which custom fields are to be fetched
     * @return array                Array to custom fields associated with the given ticket
     */
    public function customFieldValuesByTicketId($ticketId)
    {
        $ticket = $this->where('id', $ticketId)->with('customFieldValues.formfieldName')->select('id')->first();

        //if there is no custom field found for given ticketId, it assigns that as an empty array
        $customFields = !$ticket['customFieldValues'] ? [] : $ticket['customFieldValues'];

        foreach ($customFields as $customField) {
            $customField['name'] = $customField['formfieldName']['title'];
            unset($customField['formfieldName'], $customField['ticket_id'], $customField['form_field_id']);
        }
        return $customFields;
    }
    /**
     * Checks it ticket has first reply thread or not and returns boolean values
     * @param  void
     * @return boolean  $isResponded (true if first reply thread exists false otherwise)
     */
    public function firstResponseIsDone()
    {
        $isResponded = false;
        ($this->thread()->where([
            ['thread_type', '=', 'first_reply'],
            ['poster', '!=', 'client']
        ])->first()) && ($isResponded = true);
        return $isResponded;
    }

    /**
     * To get all related records from user_assign_organization table
     */
    public function ticketOrganizations()
    {
        return $this->hasMany(
            'App\Model\helpdesk\Agent_panel\User_org',
            'user_id',
            'user_id'
        );
    }

    /**
     * gets last status activity log of the ticket
     */
    public function lastStatusActivity()
    {
        return $this->hasOne('App\Model\Common\TicketActivityLog', 'ticket_id')
            ->where("category", "!=", "ticket_created")
            ->where("field","status_id")
            ->orderBy('updated_at', 'DESC');
    }

    /**
     * relationship for connecting custom field values to ticket
     */
    public function customFieldValues()
    {
        return $this->morphMany('App\Model\helpdesk\Form\CustomFormValue', 'custom');
    }
    /**
     * function to get all related records from ticket_form_data as
     * formdata() only queries record where content as not empty
     */
    public function allFormData()
    {
        //quering all fields
        return $this->hasMany('App\Model\helpdesk\Ticket\Ticket_Form_Data', 'ticket_id');
    }

    public function approvalStatus()
    {
        return $this->hasMany('App\Model\helpdesk\Workflow\ApprovalWorkflowTicket', 'ticket_id');
    }

    /**
     * Setting default priority
     */
    public function setPriorityIdAttribute($value)
    {
        if (!$value) {
            $value = TicketPriority::where('is_default', 1)->value('priority_id');
        }
        $this->attributes['priority_id'] = $value;
    }

    /**
     * Setting default source
     */
    public function setSourceAttribute($value)
    {
        if (!$value) {
            $value = TicketSource::where('name', 'Web')->value('id');
        }
        $this->attributes['source'] = $value;
    }

    /**
     * Setting default helptopic if value is invalid
     */
    public function setHelpTopicIdAttribute($value)
    {
        if (!$value) {
            // default helptopic gets stored in settings_ticket
            $value = TicketSetting::value('help_topic');
        }
        $this->attributes['help_topic_id'] = $value;
    }

    /**
     * Setting default helptopic if value is invalid
     */
    public function setDeptIdAttribute($value)
    {
        if (!$value) {
            // if help_topic_id is present, give department of helptopic
            if ($this->help_topic_id) {
                $value = HelpTopic::where('id', $this->help_topic_id)->value('department');
            } else {
                // give default department
                $value = SettingSystem::value('department');
            }
        }
        $this->attributes['dept_id'] = $value;
    }

    /**
     * Location of the ticket
     */
    public function location()
    {
        return $this->belongsTo('App\Location\Models\Location', 'location_id');
    }

    public function setStatusAttribute($value)
    {
        if (!$value) {
            // default helptopic gets stored in settings_ticket
            $value = TicketSetting::value('status');
        }
        $this->attributes['status'] = $value;
    }

    public function getEncryptedIdAttribute()
    {
        return Crypt::encryptString($this->id);
    }

    /**
     * This function retrives custom form data of a ticket in the format it is
     * recieved in ticket form submission request as shown below.
     * ['custom_field1' => 'value1', 'custom_field2' => 'value2'...]
     * It is used to pass custom form data to TicketWorkflowController while processing
     * Listeners so rules against custom form can be checked for listener enforcement.
     *
     * @return Array of formatted values or empty array
     */
    public function formattedCustomFieldValues()
    {
        return $this->customFieldValues()->select('form_field_id', 'value')->get()->transform(function ($item, $key) {
            $key = "custom_".$item['form_field_id'];
            $item['key'] = $key;
            return $item;
        })->pluck('value', 'key')->toArray();
    }

    /**
     * Function returns total time tracked on the ticket in hh:mm format
     *
     * @return  string  Total tracked time in hours:mins format
     */
    public function totalTimeTracked()
    {
        try {
            $timeArray = array_sum($this->timeTracks()->pluck('work_time')->toArray());

            return floor($timeArray/60).":".($timeArray % 60);
        } catch (\Illuminate\Database\QueryException $e) {
            /**
             * expecting QueryException because table will not be created if
             * timetrack module is not enabled
             */
            return "0:0";
        }
    }

    /**
     * Function to add ticket template variables
     * array $templateVariables
     * @return type
     */
    public function ticketTemplateVariables($messageContent = null)
    {
        $templateVariables = [];
        $ticketThread = $this->firstThread()->first();
        $templateVariables['ticket_number'] = $this->ticket_number;
        $templateVariables['ticket_link'] =  $templateVariables['system_link'] = faveoUrl('thread/' . $this->id);
        if ($ticketThread) {
            $templateVariables['ticket_subject'] = $ticketThread->title;
            $templateVariables['message_content'] = ($messageContent)?:$ticketThread->body;
        }
        ($this->duedate) ? $templateVariables['ticket_due_date'] = faveoDate($this->duedate->tz(timezone())) : '';
        ($this->created_at) ? $templateVariables['ticket_created_at'] =$this->created_at->tz(timezone()) : '';
        $assignedAgent = $this->assigned;
        if ($assignedAgent) {
            $templateVariables['agent_name'] = $assignedAgent->full_name;
            $templateVariables['agent_email'] = $assignedAgent->email;
            $templateVariables['agent_contact'] = $assignedAgent->mobile;
            $templateVariables['agent_sign'] = $assignedAgent->agent_sign;
        }
        $requester = $this->user;
        $templateVariables['client_name'] = $requester->full_name;
        $templateVariables['client_email'] = $requester->email;
        $templateVariables['client_contact'] = $requester->mobile;
        $templateVariables['department_signature'] = $this->department()->first()->department_sign;
        ($this->assignedTeam()->first()) ? $templateVariables['assigned_team_name'] = $this->assignedTeam()->first()->name : '';
        $templateVariables['ticket_client_edit_link'] = faveoUrl('ticket/' . $this->id . '/details');

        return $templateVariables;
    }

    /**
     * Function to check wether assigned agent belongs to resultant department or not.
     *
     * Warning: This method is not checking for the existance of record in user table for
     * given $assignedId. It is assumed that the call to this method is happening with
     * $assignedId which exits in database because the resultant id of an agent will be passed
     * while calling this method.
     *
     * @param   int/null  $assignedId  id of agent to whom ticket will be assigned
     * @param   int       $deptId      id of resultant department
     * @return  int/null               $assignedId or null
     *                             false
     */
    private function departmentMatchesWithAssigned(?int $assignedId, int $deptId):?int
    {

        if(!$assignedId) return null;

        // if that agent belongs to that department or he has global access
        if((DepartmentAssignAgents::where("department_id", $deptId)->where("agent_id", $assignedId)->count() || User::has('global_access', $assignedId))){
            return $assignedId;
        }

        return null;
    }

    /**
     * Accessor for duedate column
     *
     * Due date and first response due date will be same until the first response
     * is made on the ticket after which the due date for resolution is calculated
     * and updated in due date. So all updates on duedate due to SLA are applicable
     * on response_due_by. This accessor copies the value of duedate to
     * response_due_by until a first reply is made on the ticket.
     */
    public function setDuedateAttribute($value)
    {
        if(!$this->first_response_time) {
            $this->attributes['response_due_by'] = $value;
        }
        $this->attributes['duedate'] = $value;
    }

    public function approachingReminders()
    {
        return $this->belongsToMany(
            "App\Model\helpdesk\Manage\Sla\SlaApproachEscalate",
            'ticket_sla_approach_escalate',
            'ticket_id',
            'sla_approach_escalate_id'
        );
    }

    public function violatedReminders()
    {
        return $this->belongsToMany(
            "App\Model\helpdesk\Manage\Sla\SlaViolatedEscalate",
            'ticket_sla_violated_escalate',
            'ticket_id',
            'sla_violated_escalate_id'
        );
    }

    /** 
     * relation with parent ticket, when child tickets get merged to parent ticket
     */
    public function parentTicket()
    {
        return $this->hasOne(Tickets::class, 'id', 'parent_id');
    }
}
