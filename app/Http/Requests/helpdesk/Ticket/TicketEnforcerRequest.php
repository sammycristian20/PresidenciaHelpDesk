<?php

namespace App\Http\Requests\helpdesk\Ticket;

use App\Http\Requests\Request;
use App\Model\helpdesk\Ticket\TicketSla;
use App\Model\helpdesk\Ticket\TicketSlaMeta;
use App\Rules\TimeDiffValidation;
use App\Traits\CustomFieldBaseRequest;
use App\User;
use App\Traits\RequestJsonValidation;
use Auth;
use Facades\Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Lang;

/**
 * Ticket create request from client panel
 * @author  avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class TicketEnforcerRequest extends Request
{
    /**
     * This method gets called automatically everytime in FormRequest class to which Request class
     * is getting inherited. So implementing this method here throws a json response and terminate
     * further processing of request which avoids a redirect (which is the default implementation).
     *
     * @param Validator $validator
     * @returns HttpResponseException
     */
    final protected function failedValidation(Validator $validator)
    {
        //sending only the first error as object
        $errors = $validator->errors()->messages();
        $formattedErrors = [];

        foreach ($errors as $key => $message) {
            if ($key == "data.rules") {
                $this->enforceBadRequest('rules_cannot_be_empty');
            } elseif ($key == "data.actions") {
                $this->enforceBadRequest('actions_cannot_be_empty');
            } elseif ($key == "data.events") {
                $this->enforceBadRequest('events_cannot_be_empty');
            }elseif ($key == "data.name") {
                $this->enforceBadRequest(Str::snake(str_replace(['data','.'], ['',''], $message[0])));
            }
            else {
                $formattedErrors[$key] = $message[0];
            }
        }

        throw new HttpResponseException(errorResponse($formattedErrors, 400));
    }

    /**
     * throws expeception which returns validation errors as bad request
     * @param  string $messageKey
     */
    private function enforceBadRequest($messageKey)
    {
        $message = Lang::get("lang.$messageKey");
        throw new HttpResponseException(errorResponse($message, 400));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
        'type' => 'required|in:workflow,listener,sla',
        ];

        $rules['data.name'] = $this->getNameValidation($this->type);

        if ($this->type == 'workflow') {
            $rules['data.rules'] = 'required|array';
            $rules['data.actions'] = 'required|array';
            // in actions team_id and assigned_id cannot be present
            $this->validateActions();
        }

        if ($this->type == 'listener') {
            $rules['data.events'] = 'required|array';
            $rules['data.actions'] = 'required|array';
            $this->validateActions();
            $this->validateEvents();
        }

        if ($this->type == "sla") {
            $rules['data.sla_meta'] = 'required|array';
            $rules['data.sla_meta.*.priority_id'] = 'required|exists:ticket_priority,priority_id';
            $rules['data.sla_meta.*.business_hour_id'] = 'sometimes|exists:business_hours,id';
            $rules['data.sla_meta.*.respond_within'] = ['required', new TimeDiffValidation];
            $rules['data.sla_meta.*.resolve_within'] = ['required', new TimeDiffValidation];

            if($this->ifSlaRulesRequired()){
                $rules["data.rules"] = "required|array";
            }
        }

        return $rules;
    }

    /**
     * Validates events
     * @return null
     */
    private function validateEvents()
    {
        if ($this->data && isset($this->data['events']) && is_array($this->data['events'])) {
            foreach ($this->data['events'] as $event) {
              // if to and from is same but not equal to 0(any), it should be invalid
                if ($event['from'] == $event['to'] && $event['from'] != 0) {
                    throw new HttpResponseException(errorResponse(Lang::get('lang.select_different_from_to_in_events'), 400));
                }
            }
        }
    }

    /**
     * Validates actions
     * @return null
     */
    private function validateActions()
    {
        if($this->data && isset($this->data['actions']) && is_array($this->data['actions'])){

            // in actions team_id and assigned_id cannot be present
            $isAssignedAgentPresent = false;
            $isAssignedTeamPresent = false;

            foreach ($this->data['actions'] as $action) {
                if($action["field"] == "assigned_id"){
                    // if value present, then true else false
                    $isAssignedAgentPresent = (bool)$action["value"];
                }
                if($action["field"] == "team_id"){
                    // if value present, then true else false
                    $isAssignedTeamPresent = (bool)$action["value"];
                }

                if($action["field"] == "ticket_number_prefix") {
                    // if value present, then true else false
                    if (preg_match('/[^a-z0-9]{4}/i', $action["value"])){
                        throw new HttpResponseException(errorResponse(Lang::get("lang.only_four_characters_are_allowed_in_ticket_number_prefix"), 400));
                    }
                }
            }

            if($isAssignedAgentPresent && $isAssignedTeamPresent){
                throw new HttpResponseException(errorResponse(Lang::get("lang.ticket_cannot_be_assigned_to_agent_and_team_at_the_same_time"), 400));
            }
        }
    }

    /**
     * If sla rules are required
     * @return bool
     */
    private function ifSlaRulesRequired()
    {
        return (bool) !(TicketSla::where("is_default", 1)->where("id", $this->data['id'])->count());
    }

    /**
     * Gets validation string for name field
     * @param $type
     * @param $mode
     * @return string
     */
    private function getNameValidation($type)
    {
        $id = $this->data["id"];

        return "required|string|max:255|unique:".$this->getTableNameByType($type).",name,$id";
    }

    /**
     * Gets table name of enforcer by its type
     * @param $type
     * @return string
     */
    private function getTableNameByType($type)
    {
        switch ($type){
            case "workflow":
                return "ticket_workflows";

            case "listener":
                return "ticket_listeners";

            case "sla":
                return "ticket_slas";
        }
    }

}
