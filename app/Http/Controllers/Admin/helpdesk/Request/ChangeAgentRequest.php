<?php

namespace App\Http\Controllers\Admin\helpdesk\Request;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the change agent request
 */
class ChangeAgentRequest extends Request
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
     * @return array
     */
    public function rules()
    {
		$rules = [
            'email' => 'sometimes|email|unique:users,email,'.$this->id,
            'role' => 'sometimes',
            'password' => 'sometimes',
            'active' => 'sometimes'
        ];
        return $rules;
    }
}
