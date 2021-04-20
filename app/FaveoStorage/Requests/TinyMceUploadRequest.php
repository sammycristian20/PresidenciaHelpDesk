<?php

namespace App\FaveoStorage\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the StorageController@uploadHandlerForTinyMce request
 */
class TinyMceUploadRequest extends Request
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
            "file" => "required|image|mimes:jpeg,png,jpg,gif|max:20000"
        ];
    }

}
