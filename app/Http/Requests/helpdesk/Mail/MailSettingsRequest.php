<?php

namespace App\Http\Requests\helpdesk\Mail;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;
use Lang;

/**
 * validates the mailSettings request
 */
class MailSettingsRequest extends Request
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

        $this->validateHost();

        $id = $this->segment(2);

        $emailDbId = $this->input('id');
        //TODO: append fields that are not passed to the request
        $emailRules = (!$emailDbId) ? "required|email|unique:emails,email_address|unique:users,email" :
                "required|email|unique:emails,email_address,$emailDbId|unique:users,email";

        $rules = [
            'id'=>'integer|nullable',// should be null, if a new field is getting created else the id for which data has to be updated
            'email_address' => $emailRules,
            'email_name' => 'required',
            'user_name'=>'string',//can be empty
            'sending_status'=>'required',
            'fetching_status'=>'required',
            // 'password' => 'required', //password is not required in case if using services.
            'sending_protocol'=>'required_if:sending_status,1',
        ];

        $driver = $this->input('sending_protocol');
        // $driver_rules = $this->getDriver($driver);
        // $rules = array_merge($rules,$driver_rules);
        return $rules;
    }

    private function validateHost()
    {
      // checks if host is valid
      // NOTE: in case of ews, sending and fetching host should be same

      // if fetching_status in OFF or sending status is OFF, no need for validation
      if(!$this->input('fetching_status') || !$this->input('sending_status')){
        return;
      }

      if(($this->input('fetching_protocol') == 'ews' || $this->input('sending_protocol') == 'ews')
        && $this->input('sending_host') != $this->input('fetching_host')){
        throw new HttpResponseException(errorResponse(Lang::get('lang.sending_and_fetching_host_must_be_same'), 400));
      }
    }

    public function getDriver($serviceid){
        $rules = [];
        $mail_services = new \App\Model\MailJob\MailService();
        $mail_service = $mail_services->find($serviceid);
        if($mail_service){
            $short = $mail_service->short_name;
            $rules = $this->getRules($short);
        }
        return $rules;
    }

    public function getRules($short){
        $rules = [];
        switch ($short){
            case "mailgun":
                $rules =  [
                    'domain'=>'required',
                    'secret'=>'required',
                ];
                return $rules;
            case "mandrill":
                $rules =  [
                   'secret'=>'required',
                ];
                return $rules;
            default :
                return $rules;
        }
    }
}
