<?php

namespace App\TimeTrack\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class TimeTrackRequest extends FormRequest
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
        $timeTrack = $this->input('entrypoint');

        // Validation rule for description field
        $desc_valid = $timeTrack === 'popup' ? 'required|' : 'sometimes|';

        return [
            'description' => $desc_valid . 'string|max:2000',
            'work_time'   => 'required|numeric|min:0',
            'entrypoint'  => 'required|in:reply,note,popup',
        ];
    }

    /**
     * This method gets called automatically everytime in FormRequest class to which Request class 
     * is getting inherited. So implementing this method here throws a json response and terminate 
     * further processing of request which avoids a redirect (which is the default implementation). 
     * 
     * NOTE : this method should be moved to App\Http\Requests\Request class once we implement 
     *        modularity completely
     * 
     * @param Validator $validator
     * @returns HttpResponseException
     */
    final protected function failedValidation(Validator $validator) {
        //sending only the first error as object
        $errors = $validator->errors()->messages();
        $formattedErrors = [];
        foreach ($errors as $key => $message) {
            $formattedErrors[$key] = $message[0];
        }

        throw new HttpResponseException(errorResponse($formattedErrors, 422));
    }
}
