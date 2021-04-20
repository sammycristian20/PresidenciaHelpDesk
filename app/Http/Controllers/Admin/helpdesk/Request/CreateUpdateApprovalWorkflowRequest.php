<?php

namespace App\Http\Controllers\Admin\helpdesk\Request;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;
use Illuminate\Validation\Rule;

/**
 * validates the approval workflow create update request
 *
 */
class CreateUpdateApprovalWorkflowRequest extends Request
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
            'id'                            => 'sometimes|integer',
            'name'                          => ['required','string','max:255',
                Rule::unique('approval_workflows')->where(function($q){
                     $q->where('name', $this->name)->where('type', 'approval_workflow')->where('id', '!=', $this->id);
                })],

            'levels'                        => 'required|array',
            'levels.*.id'                   => 'required_with:id|integer',
            'levels.*.name'                 => 'required|string|max:255',
            'levels.*.match'                => 'required|in:all,any',
            'levels.*.order'                => 'required|integer|min:1|max:255',
            'levels.*.approvers'            => 'required|array',
            'levels.*.approvers.users'      => 'required_without:levels.*.approvers.user_types|array',
            'levels.*.approvers.user_types' => 'required_without:levels.*.approvers.users|array'
        ];

        return $rules;
    }
}