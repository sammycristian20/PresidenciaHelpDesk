<?php

namespace App\Http\Requests\helpdesk;

use App\Model\helpdesk\Settings\CommonSettings;
use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;


/**
 * Sys_userRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class Sys_userRequest extends Request
{
    use RequestJsonValidation;

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
            'first_name'    => 'required',
            'user_name'     => $this->getUsernameCheck(),
            'email'         => 'required|max:50|unique:users,email,'.$this->segment(4),
            'code'          => 'required_with:mobile|max:5',
            'mobile'        => 'max:15|unique:users,mobile,'.$this->segment(4), 
            'ext'           => 'max:5',
            'phone_number'  => 'max:15'
        ];
    }

    /**
     *  @category function to return validation rule array for username
     *  @param null
     *  @return array
     */
    public function getUsernameCheck()
    {
        
        return  [
                    'max:100',
                    'required',
                    'min:3',
                    'regex:/^(?:[A-Z\d][A-Z\d._-]{2,30}|[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4})$/i',
                    'unique:users,user_name,'.$this->segment(4)
                ];
    }
}
