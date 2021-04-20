<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

/**
 * TeamRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class TeamRequest extends Request
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
        //     $rule = 'unique:teams,name|required|max:25';
        // }
        // else{
        //     $id = $this->name;
        //     $rule = 'required|max:25|unique:teams,name,'.$id.',id';
        // }

        // return [
        //         'name'  => $rule,
               
        //     ];

        
        return [
            'name'   => 'required|unique:teams|max:30',
            'status' => 'required',
        ];
    }
}
