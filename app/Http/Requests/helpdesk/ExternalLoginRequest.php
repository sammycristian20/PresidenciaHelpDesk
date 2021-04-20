<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

/**
 * BanlistRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class ExternalLoginRequest extends Request
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
        if (Request::get('allow_external_login') == 1) {
            return [
                'redirect_unauthenticated_users_to' => 'url|required',
                'validate_token_api' => 'url|required',
                'validate_api_parameter' => 'required'
            ];
        }
        return [
            'redirect_unauthenticated_users_to' => 'url',
            'validate_token_api' => 'url',
        ];
    }
}
