<?php

namespace App\Http\Requests\helpdesk;
use App\Model\helpdesk\Settings\CommonSettings;

use App\Http\Requests\Request;

/**
 * AgentRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class AgentRequest extends Request
{
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
    public function rules() {
        return [
            'user_name' => [
                'required',
                'unique:users',
                'min:3',
                'regex:/^(?:[A-Z\d][A-Z\d._-]{2,30}|[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,5})$/i',
            ],
            'first_name' => 'required|max:30|alpha',
            'email' => getEmailValidation(),
            'active' => 'required',
            'primary_department' => 'required',
            'agent_time_zone' => 'required',
            'country_code' => 'max:5',
            'mobile' => getMobileValidation('mobile'),
            'ext' => 'max:5',
            'phone_number' => 'max:15',
        ];
    }
}
