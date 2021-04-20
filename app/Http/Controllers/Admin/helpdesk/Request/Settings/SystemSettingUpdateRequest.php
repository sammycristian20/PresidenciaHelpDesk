<?php
namespace App\Http\Controllers\Admin\helpdesk\Request\Settings;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the SystemSetting update request
 *
 */
class SystemSettingUpdateRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
		$rules = [
            'name' => 'required|max:50',
            'time_zone_id' => 'required|exists:timezone,id',
            'time_format' => 'required|exists:time_format,format',
            'date_format' => 'required|exists:date_format,format',
            'status' => 'sometimes|integer|in:0,1',
            'cdn' => 'sometimes|integer|in:0,1'
        ];

        return $rules;
    }
}
