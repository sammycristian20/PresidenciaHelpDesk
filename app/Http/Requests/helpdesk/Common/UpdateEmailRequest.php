<?php

namespace App\Http\Requests\helpdesk\Common;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the mailSettings request
 */
class UpdateEmailRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $rules = [
            'email_address' => 'required|email|unique:users,email|unique:emails,email_address',
            'oldEmail' => 'required|email',
            'password' => 'required'
        ];
        return $rules;
    }
}
