<?php

namespace App\Http\Requests;

use App\Traits\RequestJsonValidation;
use Illuminate\Foundation\Http\FormRequest;

class ImportMappingRequest extends FormRequest
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
            'faveo_attributes'=>'required',
            'import_id' => 'required',
            'faveo_attributes.*.name'=>'required',
            'faveo_attributes.*.overwrite'=>'required|boolean',
            'faveo_attributes.*.mapped_to'=>'required'
        ];
    }
}
