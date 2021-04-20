<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use App\Rules\CaptchaValidation;


/**
 * LoginRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class LoginRequest extends Request
{
    use RequestJsonValidation;

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
     *
     * @return array
     */
    public function rules()
    {


        $validationArray = [
             $this->checkField() => 'required',
            'password' => 'required',
            'g-recaptcha-response' => new CaptchaValidation(),
        ];
        return $validationArray;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            $this->checkField() . '.required' => \Lang::get('lang.the_email_or_username_field_is_required'),
        ];
    }

    public function checkField()
    {
        //if user_name params  present then will return 'user_name' else 'email will return 
       return (Request::has('user_name')) ? 'user_name' : 'email';
    }

}
