<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Model\helpdesk\Settings\CommonSettings;


class CaptchaValidation implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $secretKey = CommonSettings::where('optional_field','secret_key')->value('option_value');

        $googleSecretKey = \Request::get('google_secret_key') ? : $secretKey;

        $value = \Request::get('google_secret_key') ? \Request::get('validate_secret_key') : $value; 
        
        $googleUrl = "https://www.google.com/recaptcha/api/siteverify";

        $url = $googleUrl."?secret=".$googleSecretKey."&response=".trim($value);

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => "",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache"
          ),
        ));

        $response = curl_exec($curl);
        
        curl_close($curl);

        if(!$response || !json_decode($response)->success) {

          return false;
        }


      return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Captcha is not valid';
    }
}
