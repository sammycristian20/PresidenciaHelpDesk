<?php

namespace App\Http\Controllers\Admin\helpdesk\Request;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/**
 * validates the agent create update request
 *  * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>h
 */
class AgentCreateRequest extends Request
{
    protected function failedValidation(Validator $validator) {
        //sending only the first error as object
        $errors = $validator->errors()->messages();
        if (array_key_exists('agent_tzone_id', $errors)) {
            $errors['agent_tzone'][0] = "The agent tzone field is required.";
            unset($errors['agent_tzone_id']);
        }
    
        $formattedErrors = [];
        foreach ($errors as $key => $message) {
            $formattedErrors[$key] = $message[0];
        }
        throw new HttpResponseException(errorResponse($formattedErrors, 412));
    }

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
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
		$rules = [
            // allowing spaces in the rule
            'first_name' => 'required|max:30',
            'last_name' => 'max:30',
            'user_name' => ['required','unique:users,user_name,'.$this->id,'min:3','regex:/^(?:[A-Z\d][A-Z\d._-]{2,30}|[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,5})$/i',],
            'email' => 'required|email||unique:emails,email_address|unique:users,email,'.$this->id,
            'ext' => 'sometimes|digits_between:1,5',
            'phone_number' => 'sometimes|digits_between:1,20',
            'mobile' => 'sometimes|numeric|unique:users,mobile,'.$this->id,
            'country_code' => 'sometimes|numeric',
            'agent_tzone_id' => 'required',
            'iso' => 'sometimes|exists:country_code,iso'
        ];

        if (!filter_var($this->user_name, FILTER_VALIDATE_EMAIL) && !strrchr($this->user_name,'@')) {
            $rules['user_name'] = 'unique:users,user_name,'.$this->id.'|max:100';
        }
        return $rules;
    }
}
