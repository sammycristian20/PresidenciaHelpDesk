<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

/**
 * BanlistRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class PriorityUpdateRequest extends Request
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
        // dd(Request::url());
        //dd($this->segment('4'));
        $id=$this->segment(4);
        //dd($id);
        return [

                'priority' => 'required|max:50|unique:ticket_priority,priority,'.$id.',priority_id',
                'status' => 'required',
                'priority_desc'  => 'required|max:255',
                'priority_color' => 'required|unique:ticket_priority,priority_color,'.$id.',priority_id',
                'ispublic' => 'required',
            ];
    }
}
