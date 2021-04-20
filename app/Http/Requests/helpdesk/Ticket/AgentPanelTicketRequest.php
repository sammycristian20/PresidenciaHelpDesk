<?php

namespace App\Http\Requests\helpdesk\Ticket;

use App\Http\Requests\Request;
use App\Traits\CustomFieldBaseRequest;
use App\User;
use App\Traits\RequestJsonValidation;
use Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Lang;

use function GuzzleHttp\json_decode;

/**
 * Ticket create request from agent panel
 * @author  avinash kumar <avinash.kumar@ladybirdweb.com>
 */

class AgentPanelTicketRequest extends Request
{
    use CustomFieldBaseRequest, RequestJsonValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
      if(Auth::user() && Auth::user()->role != 'user' && User::has('create_ticket')){
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
        $this->checkTicketAssignPermission($this->assigned_id);

        $ticketValidation = $this->fieldsValidation('ticket', 'agent_panel');

        // removes all the attachments from custom fields and put it in `attachments` key
        $this->request->replace($this->getFormattedParameterWithAttachments($this->request->all()));

        // checking if requester is valid
        $this->validateRequester($ticketValidation);

        $this->captchaValidation('ticket', 'create', 'agent', $ticketValidation);

        return $ticketValidation;
    }

    /**
     * Checks if the requester is valid
     * @param $ticketValidation
     * @return null
     * @throw HttpResponseException
     */
    private function validateRequester($ticketValidation)
    {
        if (!$this->file('requester')) {
            if (!User::whereId($this->requester)->count()) {
                throw new HttpResponseException(errorResponse(['requester' => Lang::get('lang.requester_does_not_exist_create_new')], 412));
            }
        } else {
            $ticketValidation['requester'] = ['mime:csv,xlsx'];
        }
    }

    /**
     * @inheritDoc
     */
    public function prepareForValidation()
    {
        //attachments are coming from frontend as string encoded in FormData, but backend requires an associated array of file paths
        $attachmentsStringFromFormData = $this->attachments;

        if ($attachmentsStringFromFormData && is_string($attachmentsStringFromFormData)) {
            $attachmentObjectRequiredByBackend = json_decode($attachmentsStringFromFormData, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                $this->merge(['attachments' => $attachmentObjectRequiredByBackend]);
            }
        }
    }
}
