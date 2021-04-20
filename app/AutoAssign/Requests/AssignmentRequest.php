<?php

namespace App\AutoAssign\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * InstallerRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class AssignmentRequest extends Request
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
            "status" => 'required|boolean',
            "only_login" => 'required|boolean',
            "assign_not_accept" => 'required|boolean',
            "assign_with_type" => 'required|boolean',
            "is_location" => 'required|boolean',
            "assign_department_option" => 'required',
            "threshold" => 'present|min:1|max:10000000|integer',
            'department_list' => 'required_if:assign_department_option,specific|array|exists:department,id'
        ];
    }

    public function messages()
    {
        return[
            'threshold.min' => 'The number must be at least 1.',
            'threshold.max' => 'The number may not be greater than 10000000.'
        ];
    }
}
