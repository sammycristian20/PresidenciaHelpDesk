<?php
namespace App\Bill\Requests;

use App\Http\Requests\Request;

class BillRequest extends Request{
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
        return [
           'status'=>'required',
           'level'=>'required',
           'trigger_on' =>'required'
        ];
    }
}