<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

class RatingRequest extends Request
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
        //     $rule = 'unique:ratings,name|required|max:35';
        // }
        // else{
        //     $id = $this->name;
        //     $rule = 'required|max:35|unique:ratings,name,'.$id.',id';
        // }

        // return [
        //         'name'  => $rule,
        //         'display_order'      => 'required|integer',
        //         'allow_modification' => 'required',
        //         'rating_scale'       => 'required',
        //         'rating_area'        => 'required',
        //         'restrict'           => 'required',
               
        //     ];







        return [
            'name'               => 'required|unique:ratings|max:20',
            'display_order'      => 'required|unique:ratings|integer',
            'allow_modification' => 'required',
            'rating_scale'       => 'required',
            'rating_area'        => 'required',
            'restrict'           => 'required',
        ];
    }
}
