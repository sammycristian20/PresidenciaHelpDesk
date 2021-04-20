<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class WebSocketRequest extends Request
{
	use RequestJsonValidation;
	/**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'BROADCAST_DRIVER' => 'required|in:log,pusher',
			'LARAVEL_WEBSOCKETS_ENABLED'=>'required|in:true,false',
			'LARAVEL_WEBSOCKETS_PORT'=>'required',
			'LARAVEL_WEBSOCKETS_HOST'=>'required',
			'LARAVEL_WEBSOCKETS_SCHEME'=>'required|in:http,https',
			'PUSHER_APP_ID'=>'required|alpha_num',
			'PUSHER_APP_KEY'=>'required|alpha_num',
			'PUSHER_APP_SECRET'=>'required|alpha_num',
            'SOCKET_CLIENT_SSL_ENFORCEMENT'=>'required|in:true,false'
        ];
    }
}
