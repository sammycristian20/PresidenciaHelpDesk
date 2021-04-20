<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * BanRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class BusinessRequest extends Request
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


        if (Request::get('id')) {

            return [
                'id'          => 'sometimes|exists:business_hours,id',
                'name'        => 'required|max:25|unique:business_hours,name,' . Request::get('id'),
                'description' => 'required',
                'time_zone'   => 'required',
                'status'      => 'required',
                'hours'       => 'required',
            ];

        } else {
            return [
                'name'        => 'unique:business_hours,name|required|max:25',
                'description' => 'required',
                'time_zone'   => 'required',
                'status'      => 'required',
                'hours'       => 'required',
            ];
        }

    }
}
