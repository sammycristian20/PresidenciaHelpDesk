<?php

namespace App\Plugins\Envato\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Plugins\Envato\Model\Envato;

class EnvatoAuthController extends Controller
{

    public $secret;
    public $is_mandatory;
    public $envato;
    public $api;
    public $redirect_uri;
    public $client_id;
    public $allow_expired;

    public function __construct()
    {
        $this->envato        = $this->getEnvato();
        $this->secret        = $this->getSecret(); //'YaRjimpf9hnyL7OvAu70k0N4flR0MwZg';
        $this->is_mandatory  = $this->isMandatory();
        $this->api           = "https://api.envato.com";
        $this->redirect_uri  = $this->setRedirectUrl();
        $this->client_id     = $this->getClientId(); //"faveo-vahqml7l";
        $this->allow_expired = $this->allowExpired();
    }
    public function envatoCallback(Request $request)
    {
        try {
            $code = $request->input('code');
            $user = $this->getAccessToken($code);
            return redirect('envato/settings');
        } catch(\Exception $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents());
            $error = property_exists($response, 'error_description') ? $response->error_description : $response->error;
            return redirect('envato/settings')->withFails($error);
        }
    }
    public function getAccessToken($code_or_token, $grant_type = "authorization_code")
    {
        $key = 'code';
        if ($grant_type == 'refresh_token') {
            $key = 'refresh_token';
        }
        $api        = $this->api . "/token";
        $method     = "POST";
        $parameters = [
            'grant_type'    => $grant_type,
            $key            => $code_or_token,
            'client_id'     => $this->client_id,
            'client_secret' => $this->secret
        ];
        $json       = $this->guzzleClient($api, $method, $parameters);
        return $this->updateUser($json, $code_or_token, $key);
    }
    public function updateUser($json, $code, $key)
    {
        $array         = json_decode($json, true);
        $access_token  = checkArray('access_token', $array);
        $refresh_token = ($key == 'refresh_token') ? $code : checkArray('refresh_token', $array);
        $api           = $this->api . "/v1/market/private/user/account.json";
        $user_json     = $this->guzzleClient($api, 'GET', [], ['Authorization' => "Bearer " . $access_token]);
        return $this->saveUser($user_json, $access_token, $refresh_token);
    }
    public function saveUser($user_json, $access_token, $refresh_token)
    {
        Envato::first()->update([
            'access_token'   => $access_token,
            'refresh_token'  => $refresh_token,
            'envato_account' => $user_json,
        ]);
    }
    public function guzzleClient($api, $method, $parameters, $header = [])
    {
        $client = new \GuzzleHttp\Client();
        $res    = $client->request($method, $api, ['form_params' => $parameters, 'headers' => $header]);
        return $res->getBody()->getContents();
    }
    public function setRedirectUrl()
    {
        $url = url('envato/redirect');
        return $url;
    }
    public function getCodeUrl()
    {
        $url = $this->api . "/authorization?response_type=code&client_id=" . $this->client_id . "&redirect_uri=" . $this->redirect_uri;
        return $url;
    }
    public function loginService()
    {
        echo "<a class='btn btn-block btn-social btn-primary' href='" . $this->getCodeUrl() . "' style='background-color: #55ACEE;color: white;'>
                    <span class='fa fa-clock'></span> Sign in with Envato
                </a>";
    }
    public function getEnvato()
    {
        if (\Schema::hasTable('envato')) {
            $envato = Envato::first();
//            if (!$envato) {
//                throw new \Exception('Configure envato settings');
//            }
            return $envato;
        }
    }
    public function getSecret()
    {
        $envato = $this->envato;
        $secret = "";
        if ($envato) {
            $secret = $envato->token;
        }
        return $secret;
    }
    public function getClientId()
    {
        $envato = $this->envato;
        $client = "";
        if ($envato) {
            $client = $envato->client_id;
        }
        return $client;
    }
    public function isMandatory()
    {
        $envato = $this->envato;
        $check  = false;
        if ($envato && $envato->mandatory == 1) {
            $check = true;
        }
        return $check;
    }
    public function allowExpired()
    {
        $envato = $this->envato;
        $check  = false;
        if ($envato && $envato->allow_expired == 1) {
            $check = true;
        }
        return $check;
    }
    public function isSubmitTicket()
    {
        if ($this->isMandatory() && !\Auth::check()) {
            throw new \Exception('Please login to create a ticket');
        }
    }
    public function afterLogin()
    {
        $user_id = \Auth::id();
        $user    = \App\User::whereId($user_id)
                ->whereHas('userExtraField')
                ->with(['userExtraField' => function($q) {
                        $q->where('service', 'envato');
                    }])
                ->first();
        if (\Auth::user()->role == 'user' && $this->isMandatory() && (!$user || $user->userExtraField->count()
                == 0)) {
            $url = $this->getCodeUrl();
            header("Location:$url");
            exit();
        }
    }
    public function getAccount($field = '')
    {
        $account       = $this->envato->envato_account;
        $account_array = json_decode($account, true);
        $value         = checkArray('account', $account_array);
        if ($field) {
            $value = checkArray($field, $value);
        }
        return $value;
    }
}
