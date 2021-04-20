<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

class StatusUpdateRequest extends Request
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
         // dd($this->segment(2));
        return [
        'name'   => 'required|max:20|unique:ticket_status,name,'.$this->segment(2),
          
         'sort'      => 'required|min:1|integer|unique:ticket_status,order,'.$this->segment(2),


      // 'order'   => 'required|integer|unique:ticket_status,order,'.$this->segment(2),
            // 'sort'            => 'required|numeric',
        // 'icon_class'   => 'required|unique:ticket_status,icon,'.$this->segment(2),
            // 'icon_class'      => 'required',
        'icon_color'   => 'required',

        ];
    }
}
