<?php
namespace App\Bill\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class GatewaySettingRequest extends Request
{
	use RequestJsonValidation;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name'         => 'required',
            'gateway_name' => 'sometimes|required',
            'status'       => 'boolean|required',
            'is_default'   => 'boolean|required',
            'extra'        => 'sometimes|required',
            'extra.*'      => 'required_with:extra'
        ];
    }
}
