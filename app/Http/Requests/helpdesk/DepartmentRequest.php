<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

/**
 * DepartmentRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class DepartmentRequest extends Request
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
   //          $rule = 'unique:department,name|required|max:25';
   //      }
   //      else{
   //          $id = $this->name;
   //          $rule = 'required|max:25|unique:department,name,'.$id.',id';
   //      }

        return [
                'name'  => 'required|max:50|unique:department',
               
            ];


    }
}
