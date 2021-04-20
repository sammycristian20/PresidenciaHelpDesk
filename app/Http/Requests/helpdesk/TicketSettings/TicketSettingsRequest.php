<?php

namespace App\Http\Requests\helpdesk\TicketSettings;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use App\Http\Controllers\Utility\FormController;
use Validator;
use Illuminate\Validation\Rule;

/**
 * validates ticket Settings Update request
 *
 * @author Abhishek Kumar Shashi <abhishek.shashi@ladybirdweb.com>
 */
class TicketSettingsRequest extends Request {

    use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

   
    public function rules() {

        $customFields = (new FormController())->getTicketCustomFieldsFlattened();
        $customFieldId = [];
        foreach ($customFields->getData()->data as $customField) {
            $customFieldId[] = $customField->id;
        }
        return [
            'ticket_number_prefix' => ['required', 'max:8', 'min:3', 'alpha_num'],
            'status' => 'required|integer|exists:ticket_status,id,purpose_of_status,1',
            'collision_avoid' => 'required|integer',
            'record_per_page' => ['required','integer', Rule::in([10,25,50,100])],
            'lock_ticket_frequency' => 'required|integer|min:0|max:2',
            'waiting_time' => 'required|integer',
            'count_internal' => 'required|boolean',
            'show_status_date' => 'required|boolean',
            'show_org_details' => 'required|boolean',
            'show_user_location' => 'required|boolean',
            'custom_field_value' => ['array','exists:form_fields,id', Rule::in($customFieldId)],
                                    ];
    }
}
