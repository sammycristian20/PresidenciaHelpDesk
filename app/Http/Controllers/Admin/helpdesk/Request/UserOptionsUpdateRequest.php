<?php

namespace App\Http\Controllers\Admin\helpdesk\Request;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the UserOptionsRequest
 *
 */
class UserOptionsUpdateRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
		$rules = [
            'allow_users_to_create_ticket' => 'sometimes|integer|in:0,1',
            'user_set_ticket_status' => 'sometimes|integer|in:0,1',
            'user_show_cc_ticket' => 'sometimes|integer|in:0,1',
            'user_registration' => 'sometimes|integer|in:0,1',
            'user_show_org_ticket' => 'sometimes|integer|in:0,1',
            'user_reply_org_ticket' => 'sometimes|integer|in:0,1',
            'login_restrictions' => 'sometimes|array|in:email,'
        ];

        return $rules;
    }
}
