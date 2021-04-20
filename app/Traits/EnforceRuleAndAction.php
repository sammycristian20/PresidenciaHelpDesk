<?php

namespace App\Traits;

use App\Exceptions\TicketRejectionException;
use App\Model\helpdesk\Ticket\TicketRule as Rules;
use App\Model\helpdesk\Ticket\TicketAction as Actions;
use App\Model\helpdesk\Ticket\Tickets as Ticket;
use Exception;
use Lang;
use App\Http\Controllers\Common\PhpMailController;
use App\Model\helpdesk\Settings\Email as SettingEmail;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Collection;

trait EnforceRuleAndAction
{
    /**
     * Function to return rules of workflow/listener as an array in the format mentioned below
     * [rule1_field => [rule1_relation, rule1_value], rule2_field => [rule2_relation, rule2_value]]
     *
     * @param  int    $refrenceId     Id of referenced attribute
     * @param  string $refrenceType   type of referenced model
     * @return Array                  returns empty array if no related data found in database
     */
    protected  function getRules(int $refrenceId, string $refrenceType)
    {
        $refrenceType = $this->getModelByType($refrenceType);

        return Rules::where([['reference_id', '=', $refrenceId],['reference_type', '=', $refrenceType]])
          ->with(['rules' => function($q){
            $this->getNestedRules($q);
          }])->get();
    }

    /**
     * Gets query for nested actions
     * NOTE: getNestedRules and getNestedActions can be combined into one. But For better readability, please let it be seperate
     * @param  MorphMany $parentQuery
     * @return MorphMany
     */
    private function getNestedRules(MorphMany $parentQuery) : MorphMany
    {
      if($parentQuery->with('rules')->count()){
          return $parentQuery->with(['rules'=> function($q){
            $this->getNestedRules($q);
          }]);
      }
      return $parentQuery;
    }

    /**
     * Function to return actions of workflow/listener as an array in the format mentioned below
     * [action1_field => action1_value, action2_field => action2_value]
     *
     * @param  int    $refrenceId    Id of referenced attribute
     * @param  string $refrenceType  type of referenced model
     * @return Array                 returns empty array if no related data found in database
     */
    protected function getActions(int $refrenceId, string $refrenceType)
    {
        $refrenceType = $this->getModelByType($refrenceType);

        return Actions::where([['reference_id', '=', $refrenceId],['reference_type', '=', $refrenceType]])
          ->with(['actions' => function($q){
            $this->getNestedActions($q);
          }])->get();
    }

    /**
     * Gets query for nested actions
     * @param  MorphMany $parentQuery
     * @return MorphMany
     */
    private function getNestedActions(MorphMany $parentQuery) : MorphMany
    {
      if($parentQuery->with('actions')->count()){
          return $parentQuery->with(['actions'=> function($q){
            $this->getNestedActions($q);
          }]);
      }
      return $parentQuery;
    }

    /**
     * Gets model by its type
     * @param $type 'workflow', 'listener', 'sla'
     * @return string
     */
    private function getModelByType(string $type)
    {
        switch ($type){
            case 'listener':
                return 'App\Model\helpdesk\Ticket\TicketListener';

            case 'sla':
                return 'App\Model\helpdesk\Ticket\TicketSla';

            default:
                return 'App\Model\helpdesk\Ticket\TicketWorkflow';
        }
    }

    /**
     * Function to check rules one by one and return true or false on basis of Matching criteria ALL/ANY
     * (As if matching criteria is "ALL" then any of the rule among rules of workflow/listener does not match we
     * return false as all rules should match while in "ANY" if any of rules matches we return true without checking
     * further rules)
     *
     * @param  array   $rules          Array containing rules for matching in ticket
     * @param  array   $ticketValues   Array consist of ticket field and value as key pair
     * @param  string  $matcher        Matching scenario "all" or "any"
     * @return bool
     */
    protected function checkRulesAndValues(Collection $rules, array $ticketValues, string $matcher):bool
    {
        // if there is no rule, it should be considered as matched
        if(!count($rules)) return true;

        foreach ($rules as $rule) {
          // for all, if parent gets matched, check for child
          if ($matcher == 'all'){
            // if parent doesn't match, abort with false
            if(!($this->matchRuleInValues($rule, $ticketValues) && $this->checkRulesAndValues($rule->rules, $ticketValues, 'all'))){
              return false;
            }
          }

          // somewhere down the line, avoid comparing values which are empty
          if ($matcher == 'any'){
            // if parent field matches, check child fields. if child all fields are matching, return true
            if($this->matchRuleInValues($rule, $ticketValues) && $this->checkRulesAndValues($rule->rules, $ticketValues, 'all')){
              return true;
            }
          }
        }

        return $matcher == 'any' ? false : true;
    }

    /**
     * Function to check and match single rule with ticket values
     * @param   string  $key            field name to be matched in rules
     * @param   array   $values         Array containing value of fields and matching condition
     * @param   array   $ticketValues   Array consist of ticket field and value as key pair
     * @return  bool
     */
    private function matchRuleInValues(Rules $rule, array $ticketValues): bool
    {

        // if relation is negative, we will return result as true
        // REASON : if field is not present in the ticket and relation is not_equal,
        // it should be considered as positive scenario, hence should return true
        //
        // when field is not present in the ticket and relation is equal, it should
        // be considered as negative scenario, hence return false

        // if key doesn't exists at all
        if(!array_key_exists($rule->field, $ticketValues)){
          if(in_array($rule->relation, ['not_equal','dn_contains'])){
            return true;
          }
          return false;
        }

        // if exists and is empty string or empty array in rule, and is custom field, it should return TRUE,
        // But if it is null and default value, it should not return true. Because the mastermind behind faveo
        // workflow concepts was high on weed when he designed it

        // means it is a custom field
        if(strpos($rule->field, 'custom_') !== false && !$ticketValues[$rule->field]){
          return true;
        }

        // assigning to variable for better readability
        $valueInTicket = $ticketValues[$rule->field];
        $valueInRule = $rule->value;

        if(is_array($valueInTicket) && is_array($valueInRule)){
            return $this->doValuesMatchForArrayFields($valueInTicket, $valueInRule, $rule->relation);
        }

        // adding string type-hint to avoid null case exceptions
        return $this->doValuesMatch((string)$valueInTicket, (string)$valueInRule, $rule->relation);
    }

    /**
     * Check if values for tags and labels. Since rules created for them
     * @param array $valueInTicket
     * @param string $valueInRule
     * @param string|null $relation
     * @return bool
     */
    private function doValuesMatchForArrayFields(array $valueInTicket, array $valueInRule, string $relation = null)
    {
        switch ($relation){

            case "equal":
                return !count(array_diff($valueInTicket, $valueInRule)) && !count(array_diff($valueInRule, $valueInTicket));

            case "not_equal":
                return count(array_diff($valueInTicket, $valueInRule)) || count(array_diff($valueInRule, $valueInTicket));

            case "contains":
                // if all elements in $valueInRule is present in value in ticket
                return array_intersect($valueInRule, $valueInTicket) == $valueInRule;

            case "dn_contains":
                // if any of the elements in $valueInRule is not present in value in ticket
                return array_intersect($valueInRule, $valueInTicket) != $valueInRule;

            default:
                throw new \UnexpectedValueException("only equal and not_equal operations are allowed");
        }
    }

    /**
     * Function to match two strings based on given condition
     * @param   array  $values  Array containing first string and condition
     * @param   string $field   Second string to compare
     * @return  bool            true of string Comparision satisfies the condition
     */
    private function doValuesMatch(string $valueInTicket, string $valueInRule, string $relation = null):bool
    {
        // need to implement for array also
        switch ($relation) {
            case "equal":
                return (strcasecmp(strtolower($valueInTicket), strtolower($valueInRule)) == 0);

            case "not_equal":
                return !(strcasecmp(strtolower($valueInTicket), strtolower($valueInRule)) == 0);

            case "contains":
                return str_contains(strtolower($valueInTicket), strtolower($valueInRule));

            case "dn_contains":
                return !str_contains(strtolower($valueInTicket), strtolower($valueInRule));

            case "starts":
                return starts_with(strtolower($valueInTicket), strtolower($valueInRule));

            case "ends":
                return ends_with(strtolower($valueInTicket), strtolower($valueInRule));

            default :
                return false;
        }
    }

    /**
     * Function to check the given variable is an array and contains at least given number of minimum elements
     * @param  array  $array  Variable to check
     * @param  int    $min    Minimum number of elements must be present in a variable
     * @return bool           true if variable satisfies criteria, false otherwise
     */
    public function checkVariableIsAnArrayWithWithMinimumElement(array $array, int $min = 1):bool
    {
        return (!is_array($array) || count($array) < $min) ? false : true;
    }


    /**
     * Enforces action on the ticket
     * NOTE: $ticketValuesArray is passed by reference, so all modifications happens directly in this method
     * @param  Collection  $actions
     * @param  array  &$ticketValuesArray
     * @return null
     */
    public function enforceActions(Collection $actions, array &$ticketValuesArray)
    {
      foreach ($actions as $action) {

        if($action->field == 'reject_ticket'){
          throw new TicketRejectionException(Lang::get('lang.reject_ticket_exception'));
        }

        if($action->field == 'ticket_number_prefix'){
            // store it in cache, from where ticket number algorithm can pick
            quickCache('ticket_number_prefix', function() use ($action){
                return $action->value;
            });
        }

        if($action->field == 'mail_agent' || $action->field == 'mail_requester'){
          $this->enforceMailActions($action, $ticketValuesArray, new PhpMailController);
          continue;
        }

        if(is_array($action->value)){
            // whenever a field's value is an array, instead of replacing the value, we append the value to the array
            $ticketValuesArray[$action->field] = (isset($ticketValuesArray[$action->field]) && is_array($ticketValuesArray[$action->field]))
                ? array_merge($ticketValuesArray[$action->field], $action->value): $action->value;
        } else {
            $ticketValuesArray[$action->field] = $action->value;
        }

        //push workflow/listener name as approval_workflow_enforcer in $ticketValuesArray
        $this->appendApprovalEnforcer($action, $ticketValuesArray);

        //handle ticket assigment via workflows/listeners
        $this->handleAssignments($action, $ticketValuesArray);

        // recursive actions
        if($action->actions){
          $this->enforceActions($action->actions, $ticketValuesArray);
        }
      }
    }


    /**
     * Handles mail related actions.
     * NOTE: injecting phpMailController from outside, so that its test mocks can be used in assertions
     * @param  array            $action
     * @param  array             $ticketValuesArray
     * @param  PhpMailController $phpMailController
     * @return null
     */
    private function enforceMailActions(Actions $action, array $ticketValuesArray, PhpMailController $phpMailController)
    {
      $from = SettingEmail::first()->sys_email;

      $emailData = $action->actionEmail()->with('users:email')->first();

      $message = ['subject' => $emailData->subject,'scenario' => null,'body' => $emailData->body];

      if($action->field == 'mail_agent'){
        foreach ($emailData->users as $agent) {
          $to = ['name' => $agent->full_name, 'email' => $agent->email];
          $phpMailController->sendmail($from, $to, $message,[],[]);
        }
      }

      if($action->field == 'mail_requester'){
        $name = trim($ticketValuesArray['first_name'].' '.$ticketValuesArray['last_name']);

        $to = ['name' => $name, 'email' => $ticketValuesArray['email']];
        $phpMailController->sendmail($from, $to, $message,[],[]);
      }
    }

    /**
     * Function appends/updates key `approval_workflow_enforcer` and name of workflow/listener
     * as value in $ticketValuesArray to record enforcer of approval workflow which later can be
     * used to identify which listener/workflow enforced the approval workflow on the tickets.
     * This can be used to provide action performer name in ApprovalThreadHandler.
     *
     * @param   Actions  $action             current ticket action instance
     * @param   array    $ticketValuesArray  array containing ticket data values
     * @return  void
     */
    private function appendApprovalEnforcer(Actions $action, array &$ticketValuesArray):void
    {
        if($action->field == 'approval_workflow_id') {
            $ticketValuesArray['approval_workflow_enforcer'] = $action->reference()->first()->name;
        }
    }

    /**
     * Function to handle assignment to team and agents via workflow/listener actions.
     * The system does not allow to assign tickets to both team and agents. Ticket model
     * has mutators which on team_id and assigned_to attributes which sets value of the
     * attribute and sets the other as null.
     *
     * This method implements the same logic because when listener/workflow assigns tickets
     * to a team which is already assigned to an agent the mutator updates the value due to
     * use of "fill()" in TicketObserver which again sets team_id as null hence results in
     * listener not enforcing correctly.
     * @param   Actions  $action             current ticket action instance
     * @param   Array    $ticketValuesArray  array containing ticket data values
     * @return  void  
     */
    private function handleAssignments(Actions $action, array &$ticketValuesArray):void
    {
        if(!in_array($action->field, ['team_id', 'assigned_id'])) return;
        $nullKey = ($action->field == 'team_id') ? 'assigned_id': 'team_id';
        $ticketValuesArray[$nullKey] = null;
    }
}
