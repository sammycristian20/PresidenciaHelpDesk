<?php

namespace App\Http\Requests\helpdesk\SLA;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;
use Lang;

/**
 * validates the reply request from agent panel
 */
class ReminderRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'approach'=>'array',
            'violated'=>'array',

            'approach.*.reminder_delta'=>'required',
            'approach.*.type'=>'required',
            'approach.*.reminder_receivers'=>'required',
            'approach.*.reminder_receivers.agents'=>'array|required_without:approach.*.reminder_receivers.agent_types',
            'approach.*.reminder_receivers.agent_types'=>'array|required_without:approach.*.reminder_receivers.agents',

            'violated.*.reminder_delta'=>'required',
            'violated.*.type'=>['required', 'regex:/(response|resolution)/'],
            'violated.*.reminder_receivers'=>'required',
            'violated.*.reminder_receivers.agents'=>'array|required_without:violated.*.reminder_receivers.agent_types',
            'violated.*.reminder_receivers.agent_types'=>'array|required_without:violated.*.reminder_receivers.agents',
        ];

        return $rules;
    }

    /**
     * This method gets called automatically everytime in FormRequest class to which Request class
     * is getting inherited. So implementing this method here throws a json response and terminate
     * further processing of request which avoids a redirect (which is the default implementation).
     *
     * @param Validator $validator
     * @throw HttpResponseException
     */
    final protected function failedValidation(Validator $validator) {
        //sending only the first error as object
        $errors = $validator->errors()->messages();
        $formattedErrors = [];
        foreach ($errors as $key => $message) {

            if(preg_match('/\.type/', $key)){
                // these is developer level messages. will not be visible to users
                $this->errorResponse('passed type is invalid. Possible values are `response` or `resolution`');
            }

            if(preg_match('/reminder_delta/', $key)){
                // these is developer level messages. will not be visible to users
                $this->errorResponse('reminder_delta cannot be empty or zero');
            }

            if(preg_match('/(reminder_receivers.agents|reminder_receivers.agent_types|reminder_receivers)/', $key)){
                $this->errorResponse(Lang::get('lang.add_at_least_one_reminder_receiver'));
            }

            $formattedErrors[$key] = $message[0];
        }

        $this->errorResponse($formattedErrors, 412);
    }
}
