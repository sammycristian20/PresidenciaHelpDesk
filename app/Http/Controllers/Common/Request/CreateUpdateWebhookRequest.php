<?php

namespace App\Http\Controllers\Common\Request;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates webhook create or update request
 */
class CreateUpdateWebhookRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
		$rules = [
            'web_hook' => 'sometimes|url'
        ];

        return $rules;
    }
}
