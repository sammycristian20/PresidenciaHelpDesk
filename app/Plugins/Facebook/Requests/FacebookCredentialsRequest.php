<?php


namespace App\Plugins\Facebook\Requests;


use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class FacebookCredentialsRequest extends Request
{
    use RequestJsonValidation;

    public function rules()
    {
        $rules =  [
            'page_id' => 'required',
            'page_access_token' => 'required',
            'page_name' => 'required',
            'new_ticket_interval' => 'required'
        ];

        $uniqueString = '|unique:facebook_credentials';

        if ($this->getMethod() === 'POST') {
            $rules['page_id'] = $rules['page_id'] . $uniqueString;
            $rules['page_access_token'] = $rules['page_access_token']. $uniqueString;
        }

        return $rules;
    }
}