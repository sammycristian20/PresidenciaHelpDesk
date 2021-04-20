<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Lang;

/**
 * Contains method `failedValidation` which catches the validation errors before it reaches the actual
 * laravel request `failedValidation` method and convert that into a json response instead of a redirect.
 *
 * USAGE:
 * simply include this trait in your request
 */
trait RequestJsonValidation
{
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
            $formattedErrors[$key] = $message[0];
        }

        throw new HttpResponseException(errorResponse($formattedErrors, 412));
    }

    /**
     * This method gets executed whenever authorisation fails. It will throw an exception
     * which will result in a faveo formatted response to the frontend
     * @throw HttpResponseException
     */
    final protected function failedAuthorization()
    {
      throw new HttpResponseException(errorResponse(Lang::get('lang.permission_denied_action'), 400));
    }

    /**
     * Changes validation message
     * @return  array
     */
    public function messages()
    {
        return [
            '*.required' => 'This field is required',
        ];
    }
}
