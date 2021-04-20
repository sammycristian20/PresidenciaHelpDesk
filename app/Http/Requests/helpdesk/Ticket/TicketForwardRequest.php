<?php

namespace App\Http\Requests\helpdesk\Ticket;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Auth;
use Lang;

/**
 * validates the mailSettings request
 */
class TicketForwardRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $rules = [
            'emails'=>"required|array",
						'emails.*' => 'email',
						'ticket_id'=>"required|numeric|exists:tickets,id",
						'send_attachments'=>"boolean",
            'description' => 'nullable|string|max:500'
				];
		    return $rules;
    }

		public function messages()
    {
        return [
          'required'=> Lang::get('lang.this_field_is_required'),
          'emails.*'=> ':input is not a valid email address',
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
            // so that response can be made with key `emails` instead of `emails.*`
            if(strpos($key, 'emails') !== false){
							// sending key as users because frontend see that as users instead of emails
							$key = 'users';
            }
            $formattedErrors[$key] = $message[0];
        }

        throw new HttpResponseException(errorResponse($formattedErrors, 412));
    }
}
