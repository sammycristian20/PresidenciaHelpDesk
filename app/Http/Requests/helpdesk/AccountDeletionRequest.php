<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * Class sets request validator rules class for account deactivation request
 * 
 * @package App\Http\Requests\helpdesk
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 * @since v4.0.0
 */
class AccountDeletionRequest extends Request
{
    use RequestJsonValidation;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'action_on_owned_tickets' => 'required|in:delete,change_owner,nothing',
            'set_owner_to' => 'required_if:action_on_owned_tickets,change_owner|integer',
        ];
    }
}
