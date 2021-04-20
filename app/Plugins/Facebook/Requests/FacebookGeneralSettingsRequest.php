<?php


namespace App\Plugins\Facebook\Requests;


use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class FacebookGeneralSettingsRequest extends Request
{
    use RequestJsonValidation;

    public function rules()
    {
        $rules = [
            'fb_secret'        => 'required',
            'hub_verify_token' => 'required',
        ];

        $uniqueString = '|unique:facebook_general_details';

        if ($this->getMethod() === 'POST') {
            $rules['fb_secret'] = $rules['fb_secret'] . $uniqueString;
            $rules['hub_verify_token'] = $rules['hub_verify_token']. $uniqueString;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'fb_secret.unique' => trans('Facebook::lang.facebook_secret_not_unique'),
            'fb_secret.required' => trans('Facebook::lang.facebook_secret_required'),
            'hub_verify_token.required' => trans('Facebook::lang.facebook_hub_verify_token_required'),
            'hub_verify_token.unique' => trans('Facebook::lang.facebook_hub_verify_token_not_unique'),
        ];
    }
}
