<?php

namespace App\Plugins\Ldap\Request;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the mailSettings request
 */
class LdapSettingsRequest extends Request
{
    use RequestJsonValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $rules = [
            'id'=>'int',
            'domain' => "required|unique:ldap,domain,$this->id",
            'username' => 'required',
            'password' => 'required',
            'ldap_label' => 'string|max:24',
            'port' => 'nullable|numeric',
            'encryption' => 'nullable',
            'schema' => 'in:active_directory,open_ldap,free_ipa',
            'forgot_password_link' => 'string',
            'prefix' => 'string',
            'suffix' => 'string',
        ];
        return $rules;
    }
}
