<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * AgentRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class CannedRequest extends Request
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
        $validationArray = [
            'title' => 'required|max:25|unique:canned_response',
            'message' => 'required',
            'd_id[]'  => 'required_if:share,==,true'
        ];
        if (Request::get('canned_id')) {
            $validationArray['title'] = 'required|max:25|unique:canned_response,title,' . Request::get('canned_id');
            // whatever other fields are required
        }
        return $validationArray;
    }
}
