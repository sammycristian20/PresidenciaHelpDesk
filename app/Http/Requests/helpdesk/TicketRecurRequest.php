<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\CustomFieldBaseRequest;
use App\User;
use App\Traits\RequestJsonValidation;


/**
 * TicketRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class TicketRecurRequest extends Request
{
    use CustomFieldBaseRequest;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * method to rewrite failed validation message
     * @param Validator $validator
     * @return null
     * @throw HttpResponseException
     */
    protected function failedValidation(Validator $validator) {
        //sending only the first error as object
        $errors = $validator->errors()->messages();
        $formattedErrors = [];
        foreach ($errors as $key => $message) {
            $formattedErrors[$key] = $message[0];
        }

        // when recur frontend code will be rewritted for validation
        // this if condtion or method could be removed
        // only recur start date backend validation is working as of now
        // rest all required validations are handled from frontend
        if (array_key_exists('recur.start_date', $formattedErrors)) {
            $formattedErrors = 'Start With field is required.';

            throw new HttpResponseException(errorResponse($formattedErrors, 400));
        }

        throw new HttpResponseException(errorResponse($formattedErrors, 412));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //validate requester
        $requesterValidation = $this->isUserValidationRequired() ? $this->fieldsValidation('user','agent_panel') : [];

        foreach ($requesterValidation as $key => $value) {
            $requesterValidation['requester.'.$key] = $value;
            unset($requesterValidation[$key]);
        }

        $this->checkTicketAssignPermission($this->assigned_id);

        $ticketValidation = $this->fieldsValidation('ticket','agent_panel');

        $panel = (strpos(\URL::previous(), 'agent') !== false) ? 'agent' : 'admin';
        $this->captchaValidation('ticket', 'create', $panel, $validationRules);

        $recurRules = $this->addRecurRules();

        return array_merge($requesterValidation, $ticketValidation, $recurRules);
    }

    /**
     * Checks if the given user already exists in the database.
     * If yes, no validation is required so returns false,
     * else true
     * @return boolean
     */
    private function isUserValidationRequired()
    {
    	//in case of batch updload, there won't be any requester but a file
    	if($this->file('requester')){
    		return false;
    	}

        return !User::whereId($this->input('requester'))->count();
    }

    /**
     * method to add recur rules
     * @return array $recurRules
     */
    private function addRecurRules()
    {
        $recurRules = [
            'recur.name' => 'required|max:50',
            'recur.interval' => 'required',
            'recur.delivery_on' => 'sometimes',
            'recur.start_date' => 'required',
            'recur.end_date' => 'sometimes',
            'recur.execution_time' => 'sometimes'

        ];

        return $recurRules;
    }
}
