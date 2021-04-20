<?php


namespace App\Http\Requests\helpdesk\Ticket;

use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketsCategoryController;
use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use Auth;

class TicketDetailsRequest extends Request
{
    use RequestJsonValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(!Auth::user()){
            return false;
        }

        if(Auth::user()->role == 'user'){
            return false;
        }

        // check if asking ticketId can be accessed by current agent or not
        if(!(new TicketsCategoryController())->allTicketsQuery()->whereId($this->ticketId)->count()){
            return false;
        }

        return true;
    }

    /**
     * Rules for accessing current request
     * @internal there is no rule required for this request. This is a required method so, defining it
     *           with empty rules
     * @return array
     */
    public function rules()
    {
        return [];
    }

}