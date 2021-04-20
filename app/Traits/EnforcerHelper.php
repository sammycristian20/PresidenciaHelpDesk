<?php


namespace App\Traits;

use App\Http\Controllers\Common\Dependency\DependencyDetails;
use App\Http\Controllers\Utility\FormController;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\Model\helpdesk\Manage\Help_topic as HelpTopic;
use App\Model\helpdesk\Manage\UserType;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Ticket\TicketListener;
use App\Model\helpdesk\Ticket\TicketRule;
use App\Model\helpdesk\Ticket\TicketSla;
use App\Model\helpdesk\Ticket\TicketWorkflow;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use function DeepCopy\deep_copy;

/**
 * Contains helper methods for ApiEnforcerController
 */
trait EnforcerHelper
{
    /**
     * Gets parent model based on the type
     * @param string $type
     * @return string
     */
    private function getParentModel(string $type = null)
    {
        switch ($type) {
            case "listener":
                return TicketListener::class;

            case "sla":
                return TicketSla::class;

            default:
                return TicketWorkflow::class;
        }
    }

    /**
     * Saves all the listerner events
     * @param array $events
     * @param TicketListener $parent instance of TicketListener
     * @return void
     */
    private function saveEvents(array $events, TicketListener $parent)
    {
        foreach ($events as $event) {
            $parent->events()->updateOrCreate(['id'=>$event['_id']], $event);
        }
    }

    /**
     * Saves rules in the database
     * @param  array $rules     Array of the rules to be saved
     * @param  object $parent   Can be an instance of a TicketListener or a TicketWorkflow ot TicketRule which
     *                          is parent to the given rule
     * @return null
     */
    private function saveRules(array $rules, $parent)
    {
        foreach ($rules as $rule) {
            //if rule is the child one, we match the rule. For parent matching a rule is not Required
            //For eg. If rule says custom_1 => [ id=>1, node => [ custom_2 => [id=>2, node => []]]  ]
            //We don't need to check rule for custom_1 because custom_2 can only be selected if
            //custom_1 is 1
            if (isset($rule['rules']) && !$rule['rules']) {
                $rule['base_rule'] = true;
            }

            // if rule value is empty it should not be saved
            // if field is empty and it has an Id, it should be removed OR it should update that
            if ($this->shallRejectRule($rule)) {
                continue;
            }

            $parentRule = $parent->rules()->updateOrCreate(['id' => $rule['_id']], $rule);

            // the rule object has to be the parent
            if (isset($rule['rules'])) {
                //recursing over the same method to save nested nodes
                $this->saveRules($rule['rules'], $parentRule);
            }
        }
    }

    /**
     * Cleans nested rules and actions
     * @internal at frontend, when we change an option, its child never gets deleted, which cause anomalous behaviour,
     * when options are changed again
     * @param TicketWorkflow|TicketListener|TicketSla $parent
     */
    private function enforcerNestingCleanup($parent)
    {
        if (isset($parent->rules)){
            foreach ($parent->rules as $rule){
                $rule->delete();
            }
        }

        if (isset($parent->actions)){
            foreach ($parent->actions as $action){
                $action->delete();
            }
        }
    }

    /**
     * Checks if rule coming from frontend is needed to be saved
     * (if rule value is empty, it won't be saved)
     * If an existing rule comes with empty value, it will be deleted
     * @param  array  $rule
     * @return bool
     */
    private function shallRejectRule(array $rule) : bool
    {
        if (!$rule['value']) {
            // if id of the rule exists, and value is empty, it should be removed
            if ($rule['_id']) {
                TicketRule::where('id', $rule['_id'])->delete();
            }
            return true;
        }
        return false;
    }

    /**
     * Saves actions in the database
     * @param  array $actions     Array of the actions to be saved
     * @param  object $parent     Can be an instance of a TicketListener or a TicketWorkflow which is
     *                            parent to the given action
     */
    private function saveActions($actions, $parent)
    {
        foreach ($actions as $action) {
            $parentAction = $parent->actions()->updateOrCreate(['id' => $action['_id']], $action);

            if (isset($action['action_email'])) {
                $this->saveActionEmail($action['action_email'], $parentAction);
            }
            if (isset($action['actions'])) {
                $this->saveActions($action['actions'], $parentAction);
            }
        }
    }

    /**
     * Saves actionEmails in the database
     * @param  array $actionEmails     Array of the actions to be saved
     * @param  object $parent     Can be an instance of a TicketListener or a TicketWorkflow which is
     *                            parent to the given action
     */
    private function saveActionEmail($actionEmail, $parent)
    {
        // save only if the body is not empty
        if (isset($actionEmail['body'])) {
            $parentActionEmail = $parent->actionEmail()->updateOrCreate(['id' => $actionEmail['id']], $actionEmail);
            $parentActionEmail->users()->sync($actionEmail['user_ids']);
        }
    }

    /**
     * Formats ticket in required format
     * NOTE: this is a helper method for `getRecurTicket` and should not be used at any other place
     * @param $enforcer
     * @param $category
     * @throws \App\Exceptions\DependencyNotFoundException
     */
    private function formatEnforcer(&$enforcer, $category)
    {
        $formController = new FormController();

        $ruleForm = $formController->getFormFieldsForRules($category);

        isset($enforcer->rules) && $enforcer->rules->transform(function ($rule) use ($ruleForm) {
            // deep cloning so that the original form doesn't get modified in the mutation
            $ruleFormClone = deep_copy($ruleForm);

            $correspondingFormField = $this->getParentFormField($rule->field, $ruleFormClone, $rule->category);

            $rule->value = $this->getFormattedValue($rule->field, $rule->value);

            $this->bindRuleWithFormField($correspondingFormField, $rule);

            return $correspondingFormField;
        });

        $actionForm = $formController->getFormFieldsForActions($category);

        isset($enforcer->actions) && $enforcer->actions->transform(function ($action) use ($actionForm) {
            // deep cloning so that the original form doesn't get modified in the mutation
            $actionFormClone = deep_copy($actionForm);

            $correspondingFormField = $this->getParentFormField($action->field, $actionFormClone);

            $action->value = $this->getFormattedValue($action->field, $action->value);

            $this->bindActionWithFormField($correspondingFormField, $action);

            return $correspondingFormField;
        });

        $eventsTemplate = TicketListener::getEventList();

        isset($enforcer->events) && $enforcer->events->transform(function ($event) use ($eventsTemplate) {

            $eventTemplate = deep_copy($eventsTemplate->where('id', $event->field)->first());

            if ($eventTemplate->type) {
                $eventTemplate->from = $this->getFormattedValue($event->field, $event->from);
                $eventTemplate->to = $this->getFormattedValue($event->field, $event->to);
            } else {
                $eventTemplate->from = null;
                $eventTemplate->to = null;
            }
            $eventTemplate->event_id = $event->id;

            return $eventTemplate;
        });
    }

    /**
     * Gets formatted value by its key and value
     * @param string $field
     * @param string|int|null $value
     * @return array|object|string|null
     * @throws \App\Exceptions\DependencyNotFoundException
     */
    private function getFormattedValue(string $field, $value)
    {
        /*
         * NOTE FROM AVINASH: In listener events, additional "any" option is required. So adding here to reduce complexity at frontend
         */
        if ($value === 0) {
            return ['id'=> 0, 'name'=>\Lang::get('lang.any')];
        }

        $dependencyValue = null;

        $dependencyDetails = new DependencyDetails();

        $metaDependencies = ["help_topic_id", "department_id"];

        switch ($field) {
            case 'status_id':
            case 'department_id':
            case 'help_topic_id':
            case 'priority_id':
            case 'assigned_id':
            case 'location_id':
            case 'source_id':
            case 'type_id':
            case 'team_id':
            case 'approval_workflow_id':
            case 'user_id':
                $dependencyDetails->setOutputAsObject(true);
                $value = $dependencyDetails->getDependencyDetails($field, $value, in_array($field, $metaDependencies));
                break;

            case 'tag_ids':
            case 'label_ids':
            case 'organisation':
                $dependencyDetails->setOutputAsObject(false);
                $value = $dependencyDetails->getDependencyDetails($field, $value, in_array($field, $metaDependencies));
                break;

            case 'organisation_department':
                $value = OrganizationDepartment::where('id', $value)->select('id', 'org_deptname as name')->first();
        }

        // if any plugin/module want to modify or add anything to the value
        \Event::dispatch('enforcer-dependency-detail-dispatch', [$field, &$value]);

        return $value;
    }

    /**
     * gets nested nodes of rules based on parent query
     * @param  MorphMany $parentQuery
     * @return MorphMany
     */
    private function getNestedRules(MorphMany $parentQuery) : MorphMany
    {
        if ($parentQuery->with('rules')->count()) {
            return $parentQuery->with(['rules'=> function ($q) {
                $this->getNestedRules($q);
            }]);
        }
        return $parentQuery;
    }

    /**
     * gets nested nodes of action based on parent query
     * @param  MorphMany $parentQuery
     * @return MorphMany
     */
    private function getNestedActions(MorphMany $parentQuery) : MorphMany
    {
        if ($parentQuery->with('actions')->count()) {
            return $parentQuery->with(['actions'=> function ($q) {
                $this->getNestedActions($q);
            }], 'actionEmail');
        }
        return $parentQuery;
    }

    /**
     * Saves reminders
     * @param array $reminders
     * @param $parentQuery
     * @throws Exception
     */
    private function saveReminders(array $reminders, $parentQuery)
    {
        // loop over all approaching reminders and save
        foreach ($reminders['approaching'] as $approachReminder) {
            $this->saveApproachEscalation($parentQuery, (object)$approachReminder);
        }


        // loop over all approaching reminders and save
        foreach ($reminders['violated'] as $violatedReminder) {
            $this->saveViolatedEscalation($parentQuery, (object)$violatedReminder);
        }
    }

    /**
     * Saves violated Escalation
     * @param TicketSla $parentQuery
     * @param object $reminder associative array of SlaViolated object
     * @return mixed
     * @throws Exception
     */
    private function saveViolatedEscalation(TicketSla $parentQuery, object $reminder)
    {
        try {
            // should have require keys
            return $parentQuery->violatedReminders()->updateOrCreate(
                ['id'=> $reminder->id],
                [
                    'escalate_time' => $reminder->reminder_delta,
                    'escalate_type' => $reminder->type,
                'escalate_person'=> $this->getReminderReceiversAsCommaSeparated((object)$reminder->reminder_receivers)]
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage().'.Invalid payload given. Payload : '. json_encode($reminder));
        }
    }

    /**
     * Saves violated Escalation
     * @param TicketSla $parentQuery
     * @param object $reminder associative array of SlaViolated object
     * @return mixed
     * @throws Exception
     */
    private function saveApproachEscalation(TicketSla $parentQuery, object $reminder)
    {
        try {
            // should have require keys
            return $parentQuery->approachingReminders()->updateOrCreate(
                ['id'=> $reminder->id],
                [
                    'escalate_time' => $reminder->reminder_delta,
                    'escalate_type' => $reminder->type,
                    'escalate_person'=> $this->getReminderReceiversAsCommaSeparated((object)$reminder->reminder_receivers)
                ]
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage().'.Invalid payload given. Payload : '. json_encode($reminder));
        }
    }

    /**
     * Formats $reminderReceivers in a way which current DB structure supports
     * NOTE: Not changing current DB structure to avoid any conflict in remaining SLA
     * @param object $reminderReceivers
     * @return array
     */
    private function getReminderReceiversAsCommaSeparated(object $reminderReceivers) : array
    {
        $agentIds = $reminderReceivers->agents;
        $agentTypeIds = $reminderReceivers->agent_types;

        // format agent types by its type instead of id
        $userTypes = UserType::whereIn('id', $agentTypeIds)->pluck('key')->toArray();

        return array_merge($agentIds, $userTypes);
    }

    /**
     * Gets formatted reminder by modifying database keys to meaningful ones
     * @param Collection $approachReminder
     * @return Collection|\Illuminate\Support\Collection
     */
    private function getFormattedReminders(Collection $approachReminder)
    {
        // take sla id and query both
        return $approachReminder->map(function ($reminder) {

            return (object)[
                'id'=> $reminder->id,
                'reminder_delta'=> $reminder->escalate_time,
                'type'=> $reminder->escalate_type,
                'reminder_receivers'=> $this->getReminderReceiversAsArrayOfObjects($reminder->escalate_person)
            ];
        });
    }

    /**
     * Gets reminder receivers after converting comma-separated value into array of objects
     * @param array $reminderReceivers
     * @return object
     */
    private function getReminderReceiversAsArrayOfObjects(array $reminderReceivers)
    {
        $agents = User::whereIn('id', $reminderReceivers)->select('id', 'first_name', 'last_name', 'email', 'user_name')
            ->get()->map(function ($agent) {
                return (object)[
                    'id' => $agent->id,
                    'name' => $agent->full_name
                ];
            })->toArray();

        $agentTypes = UserType::whereIn('key', $reminderReceivers)->select('id', 'name')->get()->toArray();

        return (object) ['agents'=>$agents, 'agent_types'=> $agentTypes];
    }

    /**
     * Gets rules of a parent query
     * @param Builder $baseQuery
     * @return Builder
     */
    private function appendRulesQuery(Builder &$baseQuery)
    {
        return $baseQuery->with(['rules' => function ($q) {
            $q = $q->orderBy('id', 'asc');
            return $this->getNestedRules($q);
        }]);
    }

    /**
     * Gets actions of a parent query
     * @param Builder $baseQuery
     * @return Builder
     */
    private function appendActionsQuery(Builder &$baseQuery)
    {
        return $baseQuery->with(['actions'=>function ($q) {
            $q = $q->orderBy('id', 'asc');
            return $this->getNestedActions($q);
        },
            'actions.actionEmail:id,subject,body,ticket_action_id',
            'actions.actionEmail.users:users.id,first_name as name'
        ]);
    }

    /**
     * Gets reminders for parent query
     * @param Builder $baseQuery
     * @return mixed
     */
    private function appendRemindersQuery(Builder &$baseQuery)
    {
        return $baseQuery->with('approachingReminders', 'violatedReminders');
    }

    /**
     * Gets actions of a parent query
     * @param Builder $baseQuery
     * @return Builder
     */
    private function appendEventsQuery(Builder &$baseQuery)
    {
        return $baseQuery->with('events');
    }

    /**
     * Saves SLA meta data (array object of priorities with business hour)
     * @param array $metaData
     * @param TicketSla $parent
     */
    private function saveSlaMetas(array $metaData, TicketSla $parent)
    {
        foreach ($metaData as $item) {
            $parent->slaMeta()->updateOrCreate(["priority_id"=>$item["priority_id"]], [
                "respond_within"=> $item["respond_within"],
                "resolve_within"=> $item["resolve_within"],
                "business_hour_id"=> isset($item["business_hour_id"]) ? $item["business_hour_id"] : null,
                "priority_id"=>$item["priority_id"],
                "send_email_notification"=> $item["send_email_notification"],
                "send_app_notification"=> $item["send_app_notification"],
            ]);
        }
    }

    /**
     * Gets order for enforcer
     * @param $modelName
     * @return mixed
     */
    private function getOrderForEnforcer($modelName)
    {
        // if its SLA, it should exclude default entry from this
        if($modelName === TicketSla::class){
            return (int) $modelName::orderBy('order','desc')->where('is_default', '!=', 1)->value('order') + 1;
        }

        return (int) $modelName::orderBy('order','desc')->value('order') + 1;
    }
}
