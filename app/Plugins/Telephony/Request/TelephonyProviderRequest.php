<?php

namespace App\Plugins\Telephony\Request;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the mailSettings request
 */
class TelephonyProviderRequest extends Request
{
	use RequestJsonValidation;

	/**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
        	'log_miss_call' => 'boolean',
        	'iso' => 'exists:country_code,iso',
        	'conversion_waiting_time' => 'integer|min:0|max:20'
        ];
    }

}
