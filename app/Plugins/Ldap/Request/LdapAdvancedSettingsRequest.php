<?php

namespace App\Plugins\Ldap\Request;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the mailSettings request
 */
class LdapAdvancedSettingsRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $rules = [
            'faveo_attributes'=>'required',
            'faveo_attributes.*.name'=>'required',
            'faveo_attributes.*.overwrite'=>'required|boolean',
            'faveo_attributes.*.mapped_to'=>'required'
        ];
        return $rules;
    }
}
