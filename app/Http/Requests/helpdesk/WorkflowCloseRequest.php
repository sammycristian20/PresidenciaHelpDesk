<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class WorkflowCloseRequest extends Request
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
     *
     * @return array
     */
    public function rules()
    {
        return [
            'days'                   => 'required|integer|min:1',
            'send_email'             => 'required|integer',
            'status'                 => 'required|integer|exists:ticket_status,id,purpose_of_status,2',
            'rules.*.matching_value' => 'required',
            'actions.*.action'       => 'required',
            'ticket_status'          => 'required|exists:ticket_status,id'
        ];
    }
}
