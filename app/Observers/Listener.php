<?php

namespace App\Observers;

class Listener
{

    /**
     * The created rule.
     *
     * @var array
     */
    protected $rule;

    /**
     * The created execution type.
     *
     * @var string
     */
    protected $excecution;

    /**
     * The set action.
     *
     * @var array
     */
    protected $action;

    /**
     * The set event.
     *
     * @var array
     */
    protected $events;

    /**
     * The set performer.
     *
     * @var string
     */
    protected $performer;

    /**
     * The truggered event.
     *
     * @var string
     */
    protected $model_event;

    /**
     * The executed model.
     *
     * @var Tickets
     */
    protected $model;

    /**
     * The triggered entity.
     *
     * @var string
     */
    protected $triger_by;

    /**
     * The user who executed the activity.
     *
     * @var array
     */
    protected $user;

    /**
     * The orginal/database value.
     *
     * @var array
     */
    protected $orginal;

    /**
     * The changed value.
     *
     * @var array
     */
    protected $changed;

    /**
     * The changed value.
     *
     * @var string
     */
    protected $model_name;

    /**
     * The ticket model
     * 
     * @var \App\Model\helpdesk\Ticket\Tickets 
     */
    protected $ticket;

    /**
     * Set all the dependencies of a listener from database
     * @param type $listener
     */
    public function setParameters($listener)
    {
        try {
            $this->setAction($listener);
            $this->setRule($listener);
            $this->setExecution($listener);
            $this->setEvent($listener);
            $this->setTrigeredBy($listener);
            $this->setPerformer($listener);
            $this->setUser($listener);
        } catch (\Exception $e) {
            //handle the exception here
            // dd($e);
        }
    }
    /**
     * Set rules for the listener
     * @param void
     * @return void 
     */
    public function setRule($listener)
    {
        $this->rule = $listener->rules()->select('key', 'condition', 'value', 'custom_rule', 'id')->get()->toArray();
    }
    /**
     * Set the action of the listener
     * @param void
     * @return void 
     */
    public function setAction($listener)
    {
        $this->action = $listener->actions()->select('key', 'value', 'meta')->get()->toArray();
    }
    /**
     * Set the execution type for listener
     * @param void
     * @return void
     */
    public function setExecution($listener)
    {
        $this->excecution = $listener->rule_match; //all or any
    }
    /**
     * Set the performer (who doing the action) in listener model
     * @param type $listener
     * @return void
     */
    public function setPerformer($listener)
    {
        $this->performer = $listener->performed_by; //agent/requester/system
    }
    /**
     * Set the user instance, if it not present set auth user
     * @param type $listener_id
     * @param type $user
     * @return void
     */
    public function setUser($listener_id = '', $user = '')
    {
        if ($user) {
            $this->user = $user;
        }
        else {
            $this->user = \Auth::user();
        }
    }
    /**
     * Check the rules according to listener
     * @param array $ticket
     * @return array
     */
    public function checkRule()
    {
        $user    = $this->ticket->user()->select('email as rerquester_email', 'first_name as requester_name')->first()->toArray();
        $tickets = array_merge($this->ticket->toArray(), $user);
        if (!empty($user)) {
            $userData = \App\User::whereIn('email', $user)->first();
            $company['company_name'] = $userData->getUsersOrganisations()->pluck('org_id')->toArray();
            if (!empty($company)) {
                $tickets = array_merge($tickets, $company);
                $org_dept['org_dept'] = $userData->getUsersOrganisations()->where('org_department', '!=', null)->value('org_department');
                $tickets = array_merge($tickets, $org_dept);
            }
        }
        //$tickets = array_merge($tickets,$changed);//overide the values od rules
        $thread  = $this->ticket->thread()
                        ->where('poster', 'client')
                        ->where(function($q) {
                            $q->whereNotNull('title')
                            ->orWhere('title', '!=', '');
                        })
                        ->select('body', 'title as subject')
                        ->get()->toArray();
        $custom = $this->ticket->formdata()->pluck('content', 'key')->toArray();
        $ticket = array_merge(array_merge($tickets, array_collapse($thread)), $custom);
        $matched_rules  = [];
        if (count($this->rule) > 0) {
            $rules = $this->formatRulesArray($this->rule);
            $n = 0;
            foreach ($rules as $rule) {
                if (count($rule) > 1) {
                    foreach ($rule as $key => $value) {
                        $checkBox = (count($value) > 2)? true : false;
                        $key = $key;
                        $condition = $value[1];
                        $value = $value[0];
                        $matches = $this->conditions($condition, $key, $value, $ticket, $checkBox);
                        if (!$matches) {
                            $matched_rules[$n] = 0;
                            break;
                        }
                        $matched_rules[$n] = 1;
                    }
                } else {
                    $key = key($rule);
                    $condition = $rule[$key][1];
                    $value = $rule[$key][0];
                    $checkBox = (count($rule[$key]) > 2)? true : false;
                    $matched_rules[$n] = $this->conditions($condition, $key, $value, $ticket, $checkBox);
                }
                $n++;
            }
        }
        return $matched_rules;
    }
    /**
     * Identify the performer from their activity in system
     * @param type $listener_id
     */
    public function setTrigeredBy($listener_id = '')
    {
        $auth = \Auth::user();
        if ($auth && ($auth->role == 'agent' || $auth->role == 'admin')) {
            $this->triger_by = 'agent';
        }
        elseif ($this->model) {
            if (!$auth) {
                $this->triger_by = 'requester';
            }
            elseif ($auth && $auth->id == $this->model->user_id) {
                $this->triger_by = 'requester';
            }
        }
        else {
            $this->triger_by = 'system';
        }
    }
    /**
     * Calucate the condition defined in rules
     * @param string $condition
     * @param string $key
     * @param string $value
     * @param array $ticket
     * @return int
     */
    public function conditions($condition, $key, $value, $ticket, $checkBox = false)
    {
        if (!is_array($ticket) || count($ticket) == 0 || !array_key_exists($key, $ticket)) {
            return 0;
        }
        $field = (!is_array($ticket[$key])) ? preg_replace("/\s|&nbsp;/",' ', strip_tags(strtolower($ticket[$key]))) : strtolower(implode(',', $ticket[$key]));
        if ($key == 'company_name') {
            if (!is_array($ticket[$key])) return false;
            $exists = in_array($value, $ticket[$key]);
            return ($condition == 'equal') ? (int)$exists : (int)!$exists;
        }
        $condition = $this->handleMatchConditionCheckBoxFields($condition, $checkBox);
        return $this->doValuesMatch($condition, $field, $value);
    }
    /**
     * Execute the action accoding to rules abd action parameters
     * @param array $rules
     * @param array $ticket
     * @return array
     */
    public function addAction($rules, $ticket = "")
    {
        if (!$ticket) {
            $ticket = $this->model->toArray();
        }     
        if ($this->excecution($rules)) {
            foreach ($this->action as $action) {
                if ($action['key'] != 'mail' && $this->isDefaultField($action['key'])) {
                    if ($action['key'] == 'team') {
                        $ticket['team_id'] = $action['value'];
                        $ticket['assigned_to'] = null; //can be removed in future
                        continue;
                    }
                    $ticket[$action['key']] = $action['value'];
                }
                elseif ($action['key'] != 'mail' && !$this->isDefaultField($action['key'])) {
                    $ticket['formData'][$action['key']] = $action['value'];
                }
                elseif ($action['key'] == 'mail') {
                    $meta = $action['meta'];
                    $this->sendMail($meta);
                }
            }
        }
        return $ticket;
    }
    /**
     * Final execution with conditions
     * @param type $rules
     * @return boolean
     */
    public function excecution($rules)
    {
        $check = true;
        if (count($rules) > 0) {
            if ($this->excecution == 'all') {
                $counts = array_count_values($rules);
                if (checkArray(1, $counts)) {
                    if (count($rules) !== $counts[1]) {
                        $check = false;
                    }
                } else {
                    $check = false;
                }
            }
            elseif (!in_array(1, $rules)) {
                $check = false;
            }
        }
        return $check;
    }
    /**
     * Check the performer and activity owner are same
     * @return boolean
     */
    public function checkPerformer()
    {
        $check = false;
        if ($this->triger_by == $this->performer) {
            $check = true;
        }
        elseif ($this->performer == 'agent_requester') {
            if ($this->triger_by == 'agent' || $this->triger_by == 'requester') {
                $check = true;
            }
        }
        return $check;
    }
    /**
     * Set the event conditions saved in the database
     * @param type $listener_id
     * @return void
     */
    public function setEvent($listener)
    {
        $this->events = $listener->events()->select('event', 'condition', 'old', 'new')->get()->toArray();
    }
    /**
     * Check the event happened in the system and event saved in databse are same 
     * @return boolean
     */
    public function checkEvent()
    {
        $n = 0;
        while ($n < count($this->events)) {
            $point     = checkArray('event', $this->events[$n]);
            $condition = checkArray('condition', $this->events[$n]);
            $old       = checkArray('old', $this->events[$n]);
            $new       = checkArray('new', $this->events[$n]);
            $result    = $this->eventConditions($condition, $point, $old, $new);
            if ($result) {
                return $result;
            }
            $n++;
        }
        return false;
    }
    /**
     * Check the events saved conditions and activity conditions are same
     * @param string $condition
     * @param string $point
     * @param string|int $condition_old
     * @param string|int $condition_new
     * @return boolean
     */
    public function eventConditions($condition, $point, $condition_old = '', $condition_new
    = '')
    {
        $check = false;
        if (array_key_exists($point, $this->changed)) {
            if ($point == 'duedate') {
                $check = true;
            }
            elseif ($condition == 'changed' && $condition_old && $condition_new) {
                $data_new = checkArray($point, $this->changed);
                $data_old = checkArray($point, $this->orginal);
                if (is_numeric($condition_old) && is_numeric($condition_new)) {
                    $check = ($data_old == $condition_old && $data_new == $condition_new)
                                ? true : false;
                    ;
                }
                elseif (!is_numeric($condition_old) && is_numeric($condition_new)) {
                    $check = ($data_new == $condition_new) ? true : false;
                }
                elseif (is_numeric($condition_old) && !is_numeric($condition_new)) {
                    $check = ($data_old == $condition_old) ? true : false;
                }
                elseif (!is_numeric($condition_old) && !is_numeric($condition_new)) {
                    $check = true;
                }
            }
        }
        return $check;
    }
    /**
     * Get the ticket controller object
     * @return \App\Http\Controllers\Agent\helpdesk\TicketController
     */
    public function ticketController()
    {
        return new \App\Http\Controllers\Agent\helpdesk\TicketController();
    }
    public function sendMail($meta)
    {
        $receiver = checkArray('receiver', $meta);
        $subject  = checkArray('subject', $meta);
        $body     = checkArray('content', $meta);
        $user     = $this->getReciever($receiver);
        $this->sentParametersOfMailing($user, $subject, $body);
    }
    /**
     * Get the receiver of the mail in listener's action
     * @param string $receiver
     * @return boolean|array
     */
    public function getReciever($receiver)
    {
        if($this->model->getTable() == "ticket_thread"){
            switch ($receiver) {
            case "requester":
                $receiver = $requester = $this->model->ticket()->pluck('user_id')->first();
                break;
            case 'assignee':
                $receiver = $requester = $this->model->ticket()->pluck('assigned_to')->first();
                break;
            case "performer":
                $receiver = (\Auth::user()) ? \Auth::user()->id : "";
                break;
            default:
                $receiver =  \App\User::whereId($receiver)->pluck('id')->first();
            }
        }
        switch ($receiver) {
            case "requester":
                $requester = $this->model->user()->select('first_name', 'last_name', 'email')->first();
                return ($requester) ? $requester->toArray() : "";
            case 'assignee':
                $assignee  = $this->model->assigned()->select('first_name', 'last_name', 'email')->first();
                return ($assignee) ? $assignee->toArray() : "";
            case "performer":
                return (\Auth::user()) ? \Auth::user()->toArray() : "";
            default:
                $user      = \App\User::whereId($receiver)->select('first_name', 'last_name', 'email')->first();
                return ($user) ? $user->toArray() : "";
        }
    }
    /**
     * Set the parameters for mailing
     * 
     * @param array $user
     * @param string $subject
     * @param string $content
     * @return void
     */
    public function sentParametersOfMailing($user, $subject, $content)
    {
        $dept_id = 0;
        if ($this->model && $this->model->dept_id) {
            $dept_id = 1;
        }
        $from  = $this->mailController()->mailfrom(1, $dept_id);
        $email = checkArray('email', $user);
        if ($email) {
            $name               = checkArray('first_name', $user) . " " . checkArray('last_name', $user);
            $to                 = ['email' => $email, 'name' => $name];
            $message            = ['subject' => $subject, 'body' => $content, 'scenario' => null];
            $template_variables = [];
            $this->mailController()->sendmail($from, $to, $message, $template_variables);
        }
    }
    /**
     * Get the mail controller
     * 
     * @return \App\Http\Controllers\Common\PhpMailController
     */
    public function mailController()
    {
        return new \App\Http\Controllers\Common\PhpMailController();
    }
    /**
     * Get active listeners with order
     * 
     * @return \App\Model\Listener\Listener
     */
    public function getListeners()
    {
        try {
            $listeners = \App\Model\Listener\Listener::where('status', 1)
                    ->whereHas('events', function($q) {
                        $q->select('listener_id', 'event', 'condition', 'old', 'new');
                    })
                    ->whereHas('actions', function($q) {
                        $q->select('listener_id', 'key', 'value', 'meta');
                    })
                    ->with(['events', 'actions', 'rules'])
                    ->select('performed_by', 'id', 'order', 'rule_match')
                    ->orderBy('order')
                    ->get()
            ;
            return $listeners;
        } catch (\Exception $e) {
            //handle the exception here
            // dd($e);
        }
    }
    public function isDefaultField($key)
    {
        $array = [
            'priority_id',
            'type',
            'status',
            'dept_id',
            'assigned_to',
            'help_topic_id',
            'duedate',
            'body',
            'requester',
            'subject',
            'body',
            'source',
            'company',
            'team',
            'company_name',
            'org_dept'
        ];
        if (in_array($key, $array)) {
            return true;
        }
        return false;
    }

    public function formatRulesArray($compositeRules)
    {
        $rules_array = [];
        //get rules which have custom rules
        $filtered_rules_with_custom_rule = array_where($compositeRules, function ($value, $key) {
            return $value['custom_rule'] != '' || $value['custom_rule'] != null;
        });
        //get rules which do not have custom rules
        $filtered_rules_without_custom_rule = array_where($compositeRules, function ($value, $key) {
            return $value['custom_rule'] == '' || $value['custom_rule'] == null;
        });

        //create single rule for custom field's rule and save it in rules array
        foreach ($filtered_rules_with_custom_rule as $custom_rule) {
            $rules_array[] = $this->setCustomRuleArray([], $custom_rule['custom_rule'], $custom_rule['condition'], $custom_rule['value'], $compositeRules);
        }

        //add other rules in rule array
        foreach ($filtered_rules_without_custom_rule as $value) {
            $key = $value['key'];
            $key_value = $value['value'];
            $key_condition = $value['condition'];
            //check the current rule should not exist in rules array
            if (!$this->existsInRulesArray($rules_array, $key, $key_value)) {
                $rules_array[] = [$key => [$key_value, $key_condition]]; //add current rule in rules
            }
        }
        return $rules_array;
    }

    public function setCustomRuleArray($custom_rule_array, $custom_string, $codition, $value, $compositeRules)
    {
        $custom_array = json_decode($custom_string);
        if($custom_array) {
            $custom_rule_array = $this->getKeyAndValueFromCustomArray($custom_array, $custom_rule_array, $codition, $value, $compositeRules);
        }
        return $custom_rule_array;
    }

    public function getKeyAndValueFromCustomArray($custom_array, $custom_rule_array, $codition, $value, $compositeRules)
    {
        foreach ($custom_array as $array) {
            if ($array->type == 'checkbox') {
                $checkbox = '';
                foreach ($array->options as $value) {
                    if (($value->checked == 'true')) {
                        if ($this->keyExistsInRules($array->unique, $compositeRules)){
                            $checkbox .= $value->optionvalue.',';
                        }
                        if (property_exists($value, 'nodes') && count($value->nodes) > 0) {
                            $custom_rule_array = $this->getKeyAndValueFromCustomArray($value->nodes, $custom_rule_array, $codition, $value, $compositeRules);
                        }
                    }
                }
                ($checkbox != '') && ($custom_rule_array[$array->unique] = [$checkbox, $codition, 'checked']);
            } elseif ($array->type == 'radio') {
                if ($this->keyExistsInRules($array->unique, $compositeRules)){
                    $custom_rule_array[$array->unique] = [$array->value, $codition];     
                }
                if (property_exists($array, 'options')) {
                    if (count($array->options) > 0) {
                        foreach ($array->options as $option) {
                            if ($option->optionvalue == $array->value) {
                                if (property_exists($option, 'nodes') && count($option->nodes) > 0) {
                                    $custom_rule_array = $this->getKeyAndValueFromCustomArray($option->nodes, $custom_rule_array, $codition, $value, $compositeRules);
                                }
                            }
                        }
                    }
                }
            } elseif (property_exists($array, 'value')) {
                if (gettype($array->value) == 'object') {
                    if ($array->unique == 'help_topic' || $array->unique == 'department') {
                        $temp_key = ($array->unique == 'help_topic') ? 'help_topic_id' : 'dept_id';
                        if ($this->keyExistsInRules($temp_key, $compositeRules)){
                            $custom_rule_array[$temp_key] = [$value, $codition];    
                        }
                    } else {
                        if ($this->keyExistsInRules($array->unique, $compositeRules)){
                            $custom_rule_array[$array->unique] =  [$array->value->optionvalue, $codition];   
                        }
                    }
                    if (property_exists($array->value, 'nodes') && count($array->value->nodes) > 0) {
                            $custom_rule_array = $this->getKeyAndValueFromCustomArray($array->value->nodes, $custom_rule_array, $codition, $value, $compositeRules);
                    }
                } else {
                    if ($this->keyExistsInRules($array->unique, $compositeRules)){
                        $custom_rule_array[$array->unique] =  [$array->value, $codition];  
                    }
                }
            }
        }
        return $custom_rule_array;   
    }

    function existsInRulesArray($rules_array, $key, $key_value)
    {
        $flag = false;
        foreach ($rules_array as $array) {
            if (array_key_exists($key, $array) && $array[$key][0] == $key_value) {
                $flag = true;
                break;
            }
        }
        return $flag;
    }

    public function keyExistsInRules($value, $nested_array)
    {
        if (in_array($value, array_column($nested_array, 'key'))) {
            return true;
        }
        return false;
    }

    private function doValuesMatch($condition, $field, $value)
    {
        switch ($condition) {
            case "equal":
                return (int)(strcasecmp($field, strtolower($value)) == 0);
            case "not_equal":
                return !(int)(strcasecmp($field, strtolower($value)) == 0);
            case "contains":
                return (int)str_contains($field, strtolower($value));
            case "dn_contains":
                return !(int)str_contains($field, strtolower($value));
            case "starts":
                return (int)starts_with($field, strtolower($value));
            case "ends":
                return (int)ends_with($field, strtolower($value));
            default :
                return 0;
        }
    }

    private function handleMatchConditionCheckBoxFields($condition, $checkBox)
    {
        if ($checkBox) {
            if ($condition == 'starts' || $condition == 'equal') {
                return 'contains';
            } elseif ($condition == 'ends' || $condition == 'not_equal') {
                return 'dn_contains';
            }
        }
        return $condition;
    }
}
