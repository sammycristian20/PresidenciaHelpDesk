<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/**
 * ProfileRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class ProfileRequest extends Request {

    /**
     * method for handling error  manually
     * as in client panel cannot show image file error below pic
     * this method will be removed once, frontend is enhanced for new change
     * @param Validator $validator
     * @throw HttpResponseException
     */
    protected function failedValidation(Validator $validator) {
        $errors = $validator->errors()->messages();
        $formattedErrors = [];
        foreach ($errors as $key => $message) {
            $formattedErrors[$key] = $message[0];
            if ($key == 'profile_pic') {
                $message[0] = 'The profile pic must be a file of type: png, jpeg, jpg.';
                throw new HttpResponseException(errorResponse($message[0], 400));
            }
        }

        throw new HttpResponseException(errorResponse($formattedErrors, 412));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rules = [
            'first_name' => 'required',
            'mobile' => $this->checkMobile(),
            'country_code' => 'max:5',
            'ext' => 'sometimes|digits_between:1,5',
            'phone_number' => 'sometimes|digits_between:1,20',
            'profile_pic' => 'sometimes'
        ];

        /** checking image extension manually
          suppose .jpg file's extension is change to .gif
          then we can handle here, by checking original extension
         */
        if ($this->profile_pic && $this->profile_pic != 'null' && !in_array($this->profile_pic->getClientOriginalExtension(), ['png','jpeg','jpg'])) {
            $rules['profile_pic'] = 'in:png,jpeg,jpg';
        }

        return $rules;
    }

    /**
     *
     * Check the mobile number is unique or not
     * @return string
     */
    public function checkMobile() {
        $rule = 'numeric';
        if (\Auth::user()->mobile != Request::get('mobile')) {
            $rule .= '|unique:users';
        }
        if (getAccountActivationOptionValue() == "mobile" || getAccountActivationOptionValue() == "email,mobile") {
            $rule .= '|required';
        }
        return $rule;
    }
}
