<?php

namespace App\Http\Requests\kb;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class CategoryRequest extends Request
{
    use RequestJsonValidation;
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

        $validationArray = [
            'name' => 'required|max:250|unique:kb_category,name',
            'display_order' => 'required|unique:kb_category,display_order',
            'description' => 'required|max:250',
        ];
        if (Request::get('id')) {

            $validationArray['name'] = 'required|unique:kb_category,name,' . Request::get('id');

            $validationArray['display_order'] = 'required|unique:kb_category,display_order,' . Request::get('id');

            $validationArray['id'] = 'sometimes|exists:kb_category,id';
        }

        return $validationArray;
    }
}
