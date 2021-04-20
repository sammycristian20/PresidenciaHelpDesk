<?php

namespace App\Http\Requests\kb;

use App\Http\Requests\Request;

class PageUpdateRequest extends Request
{
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
//         dd(Request::url());
        $id = $this->segment(2);
        return [
            'name' => 'required|max:20',
            'slug' => 'required|max:50|unique:kb_pages,slug,'.$id,
            'description'=>'required',
        ];
    }
}
