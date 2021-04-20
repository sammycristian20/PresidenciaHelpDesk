<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;


/**
 * BanRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class BusinessUpdateRequest extends Request
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
        // dd(Request::url());
        // dd($this->segment(4));
      return [
        'name'   => 'required|max:25|unique:business_hours,name,'.$this->segment(4),
         // 'name'   => 'required|max:25|unique:business_hours,name',
            // 'name' =>'required|max:25',
            'description' => 'required',
            'time_zone' => 'required',
            'status' => 'required',
            'hours' => 'required',
        ];
    }
}