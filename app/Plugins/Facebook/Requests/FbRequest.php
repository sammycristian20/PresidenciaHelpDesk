<?php

namespace App\Plugins\Facebook\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
/**
 * validates the StorageController@ckEditorUpload request
 */
class FbRequest extends Request
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
        $rules =  [
            'app_id' => 'required|unique:facebook_app',
            'secret' => 'required|unique:facebook_app',
            'cron_confirm' => 'required',
            'new_ticket_interval' => 'required'
        ];
        if($this->getMethod() === "PUT") {
            unset($rules['app_id']);
            unset($rules['secret']);
        }
        return $rules;
    }
}