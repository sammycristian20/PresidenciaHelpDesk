<?php

namespace App\Http\Requests\helpdesk\Common;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the mailSettings request
 */
class ResendEmailVerifyRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $rules = [
            'email' => 'required|email',
        ];
        return $rules;
    }
}
