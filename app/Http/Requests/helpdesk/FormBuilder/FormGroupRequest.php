<?php

namespace App\Http\Requests\helpdesk\FormBuilder;

use App\Http\Requests\Request;
use Auth;
use App\Traits\FormFieldValidator;
use Lang;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Form group request
 * @author  avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class FormGroupRequest extends Request
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
     *
     * @return array
     */
    public function rules()
    {
        $formGroupId = $this->input('id');

        $validationRules = ['name' => 'required|unique:form_groups,name,' . $formGroupId];

        if ($formGroupId) {
            $validationRules['id'] = 'exists:form_groups';
        }

        $validationRules['department_ids'] = 'array';

        $validationRules['helptopic_ids'] = 'array';

        $validationRules['form-fields'] = 'required|array|min:1';

        $formFields = $this->input('form-fields');

        foreach ($formFields as $formField) {
            $this->validateFormField($formField);
        }

        return $validationRules;
    }

    /**
     * This method gets called automatically everytime in FormRequest class to which Request class
     * is getting inherited. So implementing this method here throws a json response and terminate
     * further processing of request which avoids a redirect (which is the default implementation).
     *
     * @param Validator $validator
     * @throw HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        //sending only the first error as object
        $errors = $validator->errors()->messages();
        $formattedErrors = [];
        foreach ($errors as $key => $message) {
            if ($key == 'form-fields') {
                throw new HttpResponseException(
                    errorResponse(Lang::get('lang.form_fields_cannot_be_empty'), 400)
                );
            }
            $formattedErrors[$key] = $message[0];
        }

        throw new HttpResponseException(errorResponse($formattedErrors, 412));
    }
}
