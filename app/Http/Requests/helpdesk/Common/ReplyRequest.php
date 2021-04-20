<?php

namespace App\Http\Requests\helpdesk\Common;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Lang;

/**
 * validates the reply request from agent panel
 */
class ReplyRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            // if we make content as required, it will stop working in double click
            'content' => 'required',

            // made it array so that muliple to can be added at later stage of the project
            'to' => 'array',

            'cc.*' => 'email',
            /**
             * @todo Remove below code snippet if billing not used anymore
             */
            'hours' => 'required_if:billable,1|regex:/^[0-9]*:[0-5][0-9]$/',

            'amount_hourly' => 'required_if:billable,1'
        ];

        return $rules;
    }

    public function messages()
    {
        return [
          'required'=> Lang::get('lang.this_field_is_required'),
          'cc.*'=> ':input is not a valid email address',
        ];
    }

    /**
     * This method gets called automatically everytime in FormRequest class to which Request class
     * is getting inherited. So implementing this method here throws a json response and terminate
     * further processing of request which avoids a redirect (which is the default implementation).
     *
     * @param Validator $validator
     * @returns HttpResponseException
     */
    final protected function failedValidation(Validator $validator) {
        //sending only the first error as object
        $errors = $validator->errors()->messages();
        $formattedErrors = [];
        foreach ($errors as $key => $message) {
            // so that response can be made with key `cc` instead of `cc.*`
            if(strpos($key, 'cc') !== false){
              $key = 'cc';
            }
            $formattedErrors[$key] = $message[0];
        }

        throw new HttpResponseException(errorResponse($formattedErrors, 412));
    }
}
