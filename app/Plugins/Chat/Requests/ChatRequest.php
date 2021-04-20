<?php
namespace App\Plugins\Chat\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
/**
 * validates the StorageController@ckEditorUpload request
 */
class ChatRequest extends Request
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
            'department' => 'required|array',
            'helptopic'  => 'required|array',
            'secret_key' => 'required'
        ];

    }

    
}