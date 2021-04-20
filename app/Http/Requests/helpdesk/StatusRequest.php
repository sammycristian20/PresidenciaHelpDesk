<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

class StatusRequest extends Request
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
           
            'name'            => 'required|max:20|unique:ticket_status',
             'sort'      => 'required|min:1|integer|unique:ticket_status,order',
            // 'order'            => 'required|numeric|unique:ticket_status',
            // 'icon_class'      => 'required',
            'icon_color'   => 'required',

        ];
    }
}
