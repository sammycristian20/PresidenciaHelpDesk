<?php

namespace App\Http\Requests\helpdesk\Ticket;

use App\Http\Requests\Request;
use App\Traits\CustomFieldBaseRequest;
use App\User;
use App\Traits\RequestJsonValidation;
use Lang;
use App\Model\helpdesk\Ticket\Tickets as Ticket;
use Illuminate\Http\Exceptions\HttpResponseException;
use Auth;

/**
 * Ticket create request from agent panel
 * @author  avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class TicketForkRequest extends Request
{
    use CustomFieldBaseRequest, RequestJsonValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

      if(Auth::check() && Auth::user()->role != 'user'){
        return true;
      }

      return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(!Ticket::where('id',  $this->id)->count()){
          throw new HttpResponseException(errorResponse(Lang::get('lang.record_not_found'), 400));
        }

        $this->checkTicketAssignPermission($this->assigned_id);

        // status_id and description is not required to be validated
        $ticketValidation = $this->fieldsValidation('ticket','agent_panel', [], true);

        // removes all the attachments from custom fields and put it in `attachments` key
        $this->request->replace($this->getFormattedParameterWithAttachments($this->request->all()));

        // now check for all permissions
        return $ticketValidation;
    }
}
