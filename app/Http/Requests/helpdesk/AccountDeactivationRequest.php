<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use App\User;

/**
 * Class sets request validator rules class for account deactivation request
 * 
 * @package App\Http\Requests\helpdesk
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 * @since v4.0.0
 */
class AccountDeactivationRequest extends Request
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
            'action_on_owned_tickets' => 'required|in:close,change_owner',
            'set_owner_to' => 'required_if:action_on_owned_tickets,change_owner|integer',
            'action_on_assigned_tickets' => 'in:surrender,change_assignee|'.$this->setRequired(),
            'set_assignee_to' => 'required_if:action_on_assigned_tickets,change_assignee|integer'
        ];
    }

    /**
     * sets the input field as required if user passed in request segment
     * is either admin or agent as only admin and agent will have tickets
     * assigned to them.
     *
     * @return string an empty string or "required"
     */
    private function setRequired():string
    {
        return in_array(User::find(Request::segments()[3])->role, ['admin','agent'])?'required':'';
    }
}
