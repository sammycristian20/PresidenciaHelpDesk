<?php
namespace App\Plugins\Whatsapp\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
/**
 * validates the StorageController@ckEditorUpload request
 */
class WhatsAppSettingsRequest extends Request
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
        $uniqueStr = "|unique:whatsapp";
        $rules =  [
            'sid' => 'required|string',
            'token'    => 'required|string',
            'business_phone'  => 'required|regex:/^[\+](\d+)(\d{9})/',
            'name' => 'required|string',
            'template' => 'nullable'
        ];
        if($this->getMethod() =='POST') {
            $rules = array_map(function($item) use ($uniqueStr){
                return $item.$uniqueStr;
            },$rules);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'business_phone.regex'=> ':input is not a valid number. Number should not contain alphabets and spaces,and must include country code. Valid Number : +918765100443',
          ];
    }
    
}