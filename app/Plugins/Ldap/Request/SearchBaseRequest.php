<?php

namespace App\Plugins\Ldap\Request;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the mailSettings request
 */
class SearchBaseRequest extends Request
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
            'search_bases'=>'array',
            'search_bases.*.search_base'=>'string|required',
            'search_bases.*.department_ids'=>'array',
            'search_bases.*.organization_ids'=>'array',
            'search_bases.*.user_type'=>'string|required',
            'import' => 'required|boolean',
        ];

        return $rules;
    }
}