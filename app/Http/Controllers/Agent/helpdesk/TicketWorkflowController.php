<?php

namespace App\Http\Controllers\Agent\helpdesk;

// controllers
use App\Http\Controllers\Controller;
// models
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Manage\Sla_plan;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Workflow\WorkflowAction;
use App\Model\helpdesk\Workflow\WorkflowName;
use App\Model\helpdesk\Workflow\WorkflowRules;
use App\User;
use Lang;

/**
 * TicketWorkflowController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class TicketWorkflowController extends Controller
{
    /**
     * constructor
     * Create a new controller instance.
     *
     * @param type TicketController $TicketController
     */
    public function __construct()
    {
        $this->TicketController = new TicketController;
    }
    public function isTarget($channel, $source)
    {

        $check = false;
        if ((int) $channel == (int) $source || $channel == 'any') {
            $check = true;
        }
        return $check;
    }
    public function relation($key, $values, $ticket_values = [])
    {
        if ((!is_array($values) || count($values) < 2) || (!is_array($ticket_values) || count($ticket_values) == 0 || !array_key_exists($key, $ticket_values)) ) {
            return false;
        }
        $field = (!is_array($ticket_values[$key])) ? preg_replace("/\s|&nbsp;/",' ', strip_tags(strtolower($ticket_values[$key]))) : strtolower(implode(',', $ticket_values[$key]));
        if ($key == 'company_name') {
            if (!is_array($ticket_values[$key])) return false;
            $exists = in_array($values[0], $ticket_values[$key]); 
            return ($values[1] == 'equal') ? $exists : !$exists;
            
        }

        $values[1] = $this->handleMatchConditionCheckBoxFields($values);
        return $this->doValuesMatch($values, $field);
    }
    
    public function workflow($fromaddress, $fromname, $subject, $body, $phone, $phonecode, $mobile_number, $helptopic, $sla, $priority, $source, $collaborator, $dept, $assign, $team_assign, $ticket_status, $form_data, $auto_response, $type, $attachment
    = "", $inline = [], $email_content = [], $company = "",$domainId="",$locationId="")
    {
        $values        = [
            'email'         => $fromaddress,
            'name'          => $fromname,
            'subject'       => $subject,
            'body'          => $body,
            'phone'         => $phone,
            'code'          => $phonecode,
            'mobile'        => $mobile_number,
            'helptopic'     => $helptopic,
            'sla'           => $sla,
            'priority'      => $priority,
            'source'        => $source,
            'cc'            => $collaborator,
            'department'    => $dept,
            'agent'         => $assign,
            'team'          => $team_assign,
            'status'        => $ticket_status,
            'custom_data'   => $form_data,
            'auto_response' => $auto_response,
            'type'          => $type,
            'attachment'    => $attachment,
            'inline'        => $inline,
            'email_content' => $email_content,
            'organization'  => $company
        ];
           //$values        = $this->process($values);
        $create_ticket = $this->TicketController->create_user($values['email'], $values['name'], $values['subject'], $values['body'], $values['phone'], $values['code'], $values['mobile'], $values['helptopic'], $values['sla'], $values['priority'], $values['source'], $values['cc'], $values['department'], $values['agent'], $values['custom_data'], $values['auto_response'], $values['status'], $values['type'], $values['attachment'], $values['inline'], $values['email_content'],$company,$domainId,'',$locationId);

        return $create_ticket;
    }
    public function process($values)
    {
        $workflow  = new WorkflowName();
        $workflows = $workflow
                ->where('status', 1)
                ->orderBy('order')
                ->get();
        $rules     = [];
        if ($workflows->count() > 0) {
            foreach ($workflows as $flow) {
                $rules[] = $this->rules($flow, $values);
            }
            $rules = head(array_filter($rules));
            if ($rules) {
                $rule = $rules->first();
                if ($rule) {
                    $actions = $this->action($rule);
                    $values  = array_replace($values, $actions);
                    $values['enforced-workflow']  = $rule->workflow->name;
                }
            }
        }
        if (key_exists('reject', $values)) {
            loging('info', Lang::get('lang.reject_ticket_exception'));
            throw new \Exception(Lang::get('lang.reject_ticket_exception_message_to_show_clients'));
        }
        return $values;
    }
    public function action($rule)
    {
        if ($rule && $rule->workflow && $rule->workflow->action) {
            $action = $rule->workflow->action->pluck('action', 'condition')->toArray();
            if (array_key_exists('priority', $action)) {
                $action['priority_id'] = $action['priority'];
                unset($action['priority']);
            }

            return $action;
        }
    }
    public function rules($workflow, $values = [])
    {
        if ($workflow) {
            $isTarget = $this->isTarget($workflow->target, $values['source']);
            if ($isTarget) {
                $work = $workflow->rule()
                        ->with(['workflow.action'])
                        ->select('id', 'workflow_id', 'matching_scenario', 'matching_relation', 'matching_value', 'custom_rule')->get();
                $rules = $this->formatRulesArray($work);
                if(!$this->matchRuleConditions($rules, $values, $workflow->rule_match)) {
                    $work = null;
                }
                if ($work && $work->count() > 0) {
                    return $work;
                }
            }
        }
    }
    
    /**
     *
     *
     *
     */
    public function formatRulesArray($work)
    {
        $compositeRules = $work->toArray();
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
            $rules_array[] = $this->setCustomRuleArray([], $custom_rule['custom_rule'], $custom_rule['matching_relation'], $custom_rule['matching_value'], $compositeRules);
        }
        //add other rules in rule array
        foreach ($filtered_rules_without_custom_rule as $value) {
            $key = $value['matching_scenario'];
            $key_value = $value['matching_value'];
            $key_condition = $value['matching_relation'];
            //check the current rule should not exist in rules array
            if (!$this->existsInRulesArray($rules_array, $key, $key_value)) {
                $rules_array[] = [$key => [$key_value, $key_condition]]; //add current rule in rules
            }
        }
        return $rules_array;
    }

    public function setCustomRuleArray($custom_rule_array, $custom_string, $codition, $matching_value, $compositeRules)
    {
        $custom_array = json_decode($custom_string);
        if($custom_array) {
            $custom_rule_array = $this->getKeyAndValueFromCustomArray($custom_array, $custom_rule_array, $codition, $matching_value, $compositeRules);
        }
        return $custom_rule_array;
    }

    public function getKeyAndValueFromCustomArray($custom_array, $custom_rule_array, $codition, $matching_value, $compositeRules)
    {
        foreach ($custom_array as $array) {
            if ($array->type == 'checkbox') {
                $checkbox = '';
                foreach ($array->options as $value) {
                    if (($value->checked == 'true')) {
                        if ($this->keyExistsInRules($array->unique, $compositeRules)){
                            ;
                            $checkbox .= $value->optionvalue.',';
                        }
                        if (property_exists($value, 'nodes') && count($value->nodes) > 0) {
                            $custom_rule_array = $this->getKeyAndValueFromCustomArray($value->nodes, $custom_rule_array, $codition, $matching_value, $compositeRules);
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
                                    $custom_rule_array = $this->getKeyAndValueFromCustomArray($option->nodes, $custom_rule_array, $codition, $matching_value, $compositeRules);
                                }
                            }
                        }
                    }
                }
            } elseif(property_exists($array, 'value')) {
                if (gettype($array->value) == 'object') {
                    if ($array->unique == 'help_topic' || $array->unique == 'department') {
                        $temp_key = ($array->unique == 'help_topic') ? 'helptopic' : 'dept_id';
                        if ($this->keyExistsInRules($temp_key, $compositeRules)){
                            $custom_rule_array[$temp_key] = [$matching_value, $codition];   
                        }
                    } else {
                        if ($this->keyExistsInRules($array->unique, $compositeRules)){
                            $custom_rule_array[$array->unique] =  [$array->value->optionvalue, $codition];   
                        }
                    }
                    if (property_exists($array->value, 'nodes') && count($array->value->nodes) > 0) {
                            $custom_rule_array = $this->getKeyAndValueFromCustomArray($array->value->nodes, $custom_rule_array, $codition, $matching_value, $compositeRules);
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

    public function matchRuleConditions($rules, $ticket_values, $method)
    {
        $matches = true;
        foreach ($rules as $rule) {
            foreach ($rule as $key => $value) {
                if (!$this->relation($key, $value, $ticket_values) && $method == 'all') {
                    $matches = false;
                    break 2;
                } elseif(!$this->relation($key, $value, $ticket_values) && $method == 'any') {
                    $matches = false;
                    break;
                } else {
                    $matches = true;
                    //check for possible cases
                }
               // dump($this->relation($key, $value, $ticket_values), $method);
            }
            if ($matches == true && $method == 'any') {
                break;
            }
        }
        return $matches;
    }

    public function keyExistsInRules($value, $nested_array)
    {
        if(in_array($value, array_column($nested_array, 'matching_scenario'))) {
            return true;
        }
        return false;
    }

    private function doValuesMatch($values, $field)
    {
        switch ($values[1]) {
            case "equal":
                return (strcasecmp($field, strtolower($values[0])) == 0);
            case "not_equal":
                return !(strcasecmp($field, strtolower($values[0])) == 0);
            case "contains":
                return str_contains($field, strtolower($values[0])); // str_contains($field, $value);
            case "dn_contains":
                return !str_contains($field, strtolower($values[0]));
            case "starts":
                return starts_with($field, strtolower($values[0]));
            case "ends":
                return ends_with($field, strtolower($values[0]));
            default :
                return false;
        }
    }

    private function handleMatchConditionCheckBoxFields($values)
    {
        if (count($values) > 2) {
            if ($values[1] == 'starts' || $values[1] == 'equal') {
                return 'contains';
            } elseif ($values[1] == 'ends' || $values[1] == 'not_equal') {
                return 'dn_contains';
            }
        }
        return $values[1];
    }
}