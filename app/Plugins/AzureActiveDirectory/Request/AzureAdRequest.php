<?php

namespace App\Plugins\AzureActiveDirectory\Request;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the mailSettings request
 */
class AzureAdRequest extends Request
{
    use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $rules = [
            'app_name'=>"required|string|unique:azure_ads,app_name,$this->id|max:50",
            'tenant_id'=>"required|string",
            'app_id'=>"required|string|unique:azure_ads,app_id,$this->id",
            'app_secret'=>"required|string",
            'login_button_label'=>"string|max:40",
        ];

        return $rules;
    }
}
