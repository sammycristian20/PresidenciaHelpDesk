<?php
namespace App\Bill\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class PackageRequest extends Request{

    use RequestJsonValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {


        if (Request::get('id')) {

            return [
                'id'          => 'sometimes|exists:packages,id',
                'name'        => 'required|max:25|unique:packages,name,' . Request::get('id'),
                'description' => 'required',
                'price'   => 'required',
                'status'      => 'required',
                // 'validity'       => 'required',
                // 'allowed_tickets'=>'required',
                'display_order' =>'required|unique:packages,display_order,'. Request::get('id'),
            ];

        } else {
            return [
                'name'        => 'unique:packages,name|required|max:25',
                'description' => 'required',
                'price'   => 'required',
                'status'      => 'required',
                // 'validity'       => 'required',
                // 'allowed_tickets'=>'required',
                'display_order' => 'required|unique:packages',
            ];
        }







       
    }
}