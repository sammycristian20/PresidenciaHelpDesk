<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

/**
 * GroupRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class GroupRequest extends Request
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
        //     $rule = 'unique:groups,name|required|max:25';
        // }
        // else{
        //     $id = $this->name;
        //     $rule = 'required|max:25|unique:groups,name,'.$id.',id';
        // }

        // return [
        //         'name'  => $rule,
               
        //     ];
        return [
            'name' => 'required|unique:groups|max:30',
        ];
    }
}
