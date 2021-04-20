<?php

namespace App\Plugins\Ldap\Request;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use Illuminate\Validation\Rule;

/**
 * validates the mailSettings request
 */
class DirectoryAttributeRequest extends Request
{
    use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $rules = [
            'id'=>'required',
            'name'=> ['required', Rule::unique('ldap_ad_attributes', 'name')->where(function ($query) {
                    return $query->where('ldap_id', $this->ldapId)->where('id', '!=', $this->id);
            })]
        ];
        return $rules;
    }
}
