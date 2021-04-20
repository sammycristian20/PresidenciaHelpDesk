<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use App\Rules\CaptchaValidation;

/**
 * RecaptchaRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class RecaptchaRequest extends Request {

    use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            
            'google_service_key' => 'required',

            'google_secret_key' => new CaptchaValidation(),
        ];
    }

}
