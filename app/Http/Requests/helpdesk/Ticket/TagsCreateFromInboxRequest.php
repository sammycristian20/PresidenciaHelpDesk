<?php

namespace App\Http\Requests\helpdesk\Ticket;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/**
 * validates the tags creation request from ticket inbox page
 */
class TagsCreateFromInboxRequest extends Request
{
    /**
     * method to update tags validation message
     * @param Validator $validator
     * @return null
     */
    protected function failedValidation(Validator $validator) {
        $errors = $validator->errors()->messages();
    
        $this->updateTagsValidationMessage($errors);

        foreach ($errors as $key => $message) {
            $errors[$key] = reset($message);
        }
    
        throw new HttpResponseException(errorResponse($errors, 412));
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $rules = [
            'ticket_id' => 'sometimes|numeric|exists:tickets,id',
            'tags' => 'sometimes|array',
            'tags.*' => 'sometimes|max:20'
        ];
        return $rules;
    }

    /**
     * method to upadte tags validation message
     * @param array $errors
     * @return null
     */
    private function updateTagsValidationMessage(array &$errors) {
        $tagValidationMessages = array_filter(array_keys($errors), function($error){
                            if (strpos($error, 'tags') === 0) {
                                return $error;
                            }
                        });

        if ($tagValidationMessages) {
            $errors['tags'] = ['Tags should be less than 20 characters.'];
            array_walk($tagValidationMessages, function($messageKey) use(&$errors){
                unset($errors[$messageKey]);
            });
        }
    }
}
