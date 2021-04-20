<?php

namespace App\Plugins\SMS\Requests;

use App\Http\Requests\Request;

/**
* 
*/
class CustomMessageRequest extends Request
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
    	return [
    		'name' => 'required|min:6',
    		'content' => 'required',
    		'send-at'=> 'required',
    		'send_to_clients' => 'required',
    		'send_to_agents' => 'required',
    	];
    }
}