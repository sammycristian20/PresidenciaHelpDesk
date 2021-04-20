<?php

namespace App\Http\Controllers\Common\Request;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates API setting update request
 */
class UpdateApiSettingRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
		$rules = [
            'api_enable' => 'sometimes|integer|in:0,1',
            'api_key_mandatory' => 'sometimes|integer|in:0,1',
            'api_key' => 'sometimes|min:10|max:32'
        ];
        
        return $rules;
    }
}
