<?php

namespace App\Http\Requests\helpdesk\Requester;

use App\Traits\CustomFieldBaseRequest;
use App\Traits\RequestJsonValidation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\Request;
use DB;
use Auth;
use Lang;

/**
 * Register requester from client/agent panel
 * @author  avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class UserRegisterRequest extends Request
{
    use CustomFieldBaseRequest;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Auth::user() && Auth::user()->role != 'user'){
          return true;
        }

        $isUserRegistrationAllowed = DB::table('common_settings')->where('option_name','user_registration')
          ->where('status', 1)->count();

        if($isUserRegistrationAllowed){
          return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // NOTE: currently there is only one API which handles both agent and client panel
        // registrations with organisation and organisation department. This is a workaround
        // for the time being and has to be changed after first version is released
        $panel = $this->input('panel') ? $this->input('panel') : 'client';

        $userValidation = $this->fieldsValidation('user',$panel.'_panel');

        // removes all the attachments from custom fields and put it in `attachments` key
        $this->request->replace($this->getFormattedParameterWithAttachments($this->request->all()));

        // default user validations
        $defaultRules = [
          'email' => 'unique:users,email|unique:users,user_name|unique:emails,email_address',
          'user_name' => 'required_without_all:email,mobile|unique:users,user_name|unique:users,email|unique:emails,email_address',
          'mobile' => "unique:users,mobile|min:7|max:20",
          'phone_number' => "sometimes|digits_between:1,20",
        ];

        $this->captchaValidation('user', 'create', $panel, $userValidation);

        return array_merge($userValidation, $defaultRules);
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
            'user_name.required_without_all' => 'Username is required in absence of email or mobile',
            'email.*' =>'This email/username has already been taken',
            'user_name.*' => 'This email/username has already been taken',
        ];
    }
}
