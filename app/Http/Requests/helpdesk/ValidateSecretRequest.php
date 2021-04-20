<?php

namespace App\Http\Requests\helpdesk;

use Cache;
use Crypt;
use Google2FA;
use App\User;
use App\Http\Requests\Request;
use Illuminate\Validation\Factory as ValidatonFactory;

class ValidateSecretRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'totp' => 'bail|required|digits:6',
            'PPAuth' => 'required|array'
        ];
    }
}
