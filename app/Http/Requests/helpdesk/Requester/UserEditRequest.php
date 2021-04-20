<?php

namespace App\Http\Requests\helpdesk\Requester;

use App\Traits\CustomFieldBaseRequest;
use App\Traits\RequestJsonValidation;
use App\Http\Requests\Request;

/**
 * Register requester from client/agent panel
 * @author  avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class UserEditRequest extends Request
{
    use CustomFieldBaseRequest, RequestJsonValidation;

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
        // NOTE: currently there is only one API which handles both agent and client panel
        // registrations with organisation and organisation department. This is a workaround
        // for the time being and has to be changed after first version is released
        $panel = $this->input('panel') ? $this->input('panel') : 'client';

        $userValidation = $this->fieldsValidation('user',$panel.'_panel');

        // removes all the attachments from custom fields and put it in `attachments` key
        $this->request->replace($this->getFormattedParameterWithAttachments($this->request->all()));

        $id = $this->id;

        // default user validations
        $defaultRules = [
          'email' => "unique:users,email,$id,id",
          'user_name' => "unique:users,user_name,$id,id",
          'mobile' => "unique:users,mobile,$id,id|min:7|max:20",
          'phone_number' => "sometimes|digits_between:1,20",
        ];

        $this->captchaValidation('user', 'edit', $panel, $userValidation);

        return array_merge($userValidation, $defaultRules);
    }
}
