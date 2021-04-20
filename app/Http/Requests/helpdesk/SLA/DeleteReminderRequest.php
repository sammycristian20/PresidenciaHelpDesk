<?php

namespace App\Http\Requests\helpdesk\SLA;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 * validates the reply request from agent panel
 */
class DeleteReminderRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'id'=>'required|int',
            'type'=>['required', 'regex:/(violated|approach)/'],
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

            if(preg_match('/type/', $key)){
                // these is developer level messages. will not be visible to users
                $this->errorResponse('passed type is invalid. Possible values are `approach` or `violated`');
            }

            $formattedErrors[$key] = $message[0];
        }

        $this->errorResponse($formattedErrors, 412);
    }
}
