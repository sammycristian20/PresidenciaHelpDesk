<?php

namespace App\Http\Requests\helpdesk\Common\FormBuilder;

use App\Http\Requests\Request;
use App\Traits\FormFieldValidator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Auth;
use Lang;

class FormFieldRequest extends Request
{
    use FormFieldValidator;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!Auth::check()) {
            return false;
        }

        if (Auth::user()->role != 'admin') {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $formFields = $this->input('form-fields');

        $categoryName = $this->input('category');

        if ($categoryName == 'user') {
            $this->validateForUniqueField($formFields);
        }

        // scan through the form fields,
        // go to all labels and see if nothing is empty
        // go through all options and see if none is empty
        // go though all nodes and repeat the same
        foreach ($formFields as $formField) {
            $this->validateFormField($formField);
        }

        return ['form-fields' => 'required|array'];
    }

    /**
     * Checks if one of unique field for user creation is given
     * @return null
     */
    private function validateForUniqueField(array $formFields)
    {
        $isUniqueFieldChoosenForAgent = false;
        $isUniqueFieldChoosenForClient = false;

        foreach ($formFields as $formField) {

            // check if Email or Username is required
            if ($formField['title'] == 'Email' || $formField['title'] == 'User Name') {

                if ($formField['required_for_user']) {
                    $isUniqueFieldChoosenForClient = true;
                }

                if ($formField['required_for_agent']) {
                    $isUniqueFieldChoosenForAgent = true;
                }
            }
        }

        if (!$isUniqueFieldChoosenForAgent || !$isUniqueFieldChoosenForClient) {
            throw new HttpResponseException(errorResponse(Lang::get('lang.atleast_username_or_email_has_to_be_a_required_field'), 400));
        }
    }
}
