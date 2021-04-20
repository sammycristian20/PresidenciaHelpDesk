<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

/**
 * BanlistRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class PriorityRequest extends Request
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
        // if($this->method()=='post'){
        //     $rule = 'unique:ticket_priority,priority|required|max:10';
        // }
        // else{
        //     $id = $this->priority;
        //     $rule = 'required|max:10|unique:ticket_priority,priority,'.$id.',priority_id';
        // }

        return [
                'priority'  => 'required|max:50|unique:ticket_priority',
                'status' => 'required',
                'priority_desc'  => 'required|max:255',
                'priority_color' => 'required|unique:ticket_priority',
                'ispublic' => 'required',
            ];
    }
}
