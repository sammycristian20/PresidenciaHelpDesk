<?php

namespace App\Http\Controllers\Admin\helpdesk\Request;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the approval workflow index page request
 *
 */
class ApprovalWorkflowListRequest extends Request
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
            'limit' => 'sometimes|integer',
            'page'     => 'sometimes|integer',
            'sort_by'  => 'sometimes|string|in:name,created_at,updated_at,id',
            'order'    => 'sometimes|string|in:asc,desc',
            'search'   => 'sometimes|string',
            'restricted' => 'sometimes|boolean'
        ];

        return $rules;
    }
}