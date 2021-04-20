<?php

namespace App\Http\Requests\kb;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use App\Rules\CaptchaValidation;

class CommentRequest extends Request
{
    use RequestJsonValidation;


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
       $validationArray = [
            'name'    => 'required|max:50',
            'email'   => 'required|email',
            'website' => 'url',
            'comment' => 'required|max:500',
            'g-recaptcha-response' => new CaptchaValidation(),
        ];

        return $validationArray;
    }
}
