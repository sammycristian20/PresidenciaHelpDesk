<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

/**
 * HelptopicRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class HelptopicRequest extends Request
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
        //     $rule = 'unique:help_topic,topic|required|max:25';
        // }
        // else{
        //     $id = $this->topic;
        //     $rule = 'required|max:25|unique:help_topic,topic,'.$id.',id';
        // }

        // return [
        //         'topic'  => $rule,
        //        'department' => 'required',
        //     ];



        
        return [
            'topic' => 'required|unique:help_topic|max:50',
            // 'parent_topic' => 'required',
            // 'custom_form' => 'required',
            'department' => 'required',
            'linked_departments' => 'required'
                // 'auto_assign' => 'required',
        ];
    }
}
