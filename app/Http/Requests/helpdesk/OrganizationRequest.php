<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

/**
 * OrganizationRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class OrganizationRequest extends Request
{
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
     *
     * @return array
     */
    public function rules()
    {
        return [

            'organization_name'    => 'required|max:100|unique:organization',
            // 'website' => 'required|url|max:30',
            'phone' => 'max:20',

            // required|max:30|min:3|unique:users,user_name,
        ];
    }
}

