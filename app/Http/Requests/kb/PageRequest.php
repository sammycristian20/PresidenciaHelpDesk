<?php

namespace App\Http\Requests\kb;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class PageRequest extends Request {

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
            'name' => 'required|max:25|unique:kb_pages',
            'slug' => 'max:50|required|unique:kb_pages',
            'description' => 'required',
        ];
        if (Request::get('pageid')) {
            $validationArray['pageid'] = 'sometimes|exists:kb_pages,id';

            $validationArray['slug'] = 'max:50|required|unique:kb_pages,slug,' . Request::get('pageid') . ',id';

            $validationArray['name'] = 'required|max:25|unique:kb_pages,name,' . Request::get('pageid');
        }

        return $validationArray;
    }

}
