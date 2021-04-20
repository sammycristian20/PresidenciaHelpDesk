<?php

namespace App\Http\Requests\helpdesk\Ticket;

use App\Http\Requests\Request;
use App\Traits\CustomFieldBaseRequest;
use App\User;
use App\Traits\RequestJsonValidation;
use App\Policies\TicketPolicy;
use Lang;
use App\Model\helpdesk\Ticket\Tickets as Ticket;
use Illuminate\Http\Exceptions\HttpResponseException;
use Auth;

/**
 * Ticket create request from agent panel
 * @author  avinash kumar <avinash.kumar@ladybirdweb.com>
 */

class TicketEditRequest extends Request
{
    use CustomFieldBaseRequest, RequestJsonValidation;

    /**
     * ticket which is getting edited
     * @var Ticket
     */
    private $ticket;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

      if(!(new TicketPolicy)->edit()){
        return false;
      }

      // check if department is changed. If yes, check for permission
      if($this->isChangingDepartment() && !(new TicketPolicy)->transfer()){
        return false;
      }

      return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // status_id and description is not required to be validated
        $ticketValidation = $this->fieldsValidation('ticket','agent_panel', [], true);

        // removes all the attachments from custom fields and put it in `attachments` key
        $this->request->replace($this->getFormattedParameterWithAttachments($this->request->all()));

        $this->captchaValidation('ticket', 'edit', 'agent', $ticketValidation);

        // now check for all permissions
        return $ticketValidation;
    }

    /**
     * If department is getting changed
     * @return bool
     */
    private function isChangingDepartment() : bool
    {
      $ticket = Ticket::where('id', $this->id)->select('dept_id')->first();

      if(!$ticket){
        throw new HttpResponseException(errorResponse(Lang::get('lang.record_not_found'), 400));
      }

      if($this->has('department_id') && $ticket->dept_id != $this->input('department_id')){
        return true;
      }
      return false;
    }
}
