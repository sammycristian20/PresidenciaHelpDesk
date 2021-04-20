<?php

namespace App\Http\Requests\helpdesk\Ticket;

use App\Http\Requests\Request;
use App\Traits\CustomFieldBaseRequest;
use App\User;
use App\Traits\RequestJsonValidation;
use Auth;
use DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Lang;

/**
 * Ticket create request from client panel
 * @author  avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class ClientPanelTicketRequest extends Request
{
    use CustomFieldBaseRequest, RequestJsonValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Auth::user()){
          return true;
        }

        // if user is not logged in, he should not be able to create ticket if it is not allowed
        $isTicketCreationAllowed = DB::table('common_settings')->where('status', 1)
            ->where('option_name', 'allow_users_to_create_ticket')->count();

        if($isTicketCreationAllowed){
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
        // checking if requester is valid
        $this->validateRequester();

        $ticketValidation = $this->fieldsValidation('ticket','client_panel');

        // removes all the attachments from custom fields and put it in `attachments` key
        $this->request->replace($this->getFormattedParameterWithAttachments($this->request->all()));

        $this->captchaValidation('ticket', 'create', 'client', $ticketValidation);

        return $ticketValidation;
    }

    /**
     * Checks if the requester is valid
     * @return null
     * @throw HttpResponseException
     */
    private function validateRequester()
    {
        if(!User::where("email",$this->requester)->orWhere("user_name", $this->requester)->count()){
            throw new HttpResponseException(errorResponse(['requester' => Lang::get('lang.requester_does_not_exist_create_new')], 412));
        }
    }
}
