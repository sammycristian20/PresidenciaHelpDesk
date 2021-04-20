<?php

namespace App\FaveoStorage\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;
use Lang;

/**
 * validates the StorageController@ckEditorUpload request
 */
class CkEditorRequest extends Request
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
        return [
            "upload" => "required|image|mimes:jpeg,png,jpg,gif|max:20000"
        ];
    }

}
