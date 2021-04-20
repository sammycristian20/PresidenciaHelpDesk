<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

/**
 * OrganizationUpdate.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class OrganizationUpdate extends Request
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
           'name'   => 'required|max:100|unique:organization,name,'.$this->segment(4),
            // 'website' => 'required|url|max:30',
            'phone' => 'max:20',
            // 'website' => 'url',
                // 'phone' => 'size:10',
        ];
    }
}
