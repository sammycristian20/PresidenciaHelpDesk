<?php

namespace App\Plugins\Envato\Controllers;

use App\Plugins\Envato\Controllers\EnvatoAuthController;

/**
 * Envato Market API
 *
 * @author Ladybird Web Solution <info@ladybirdweb.com>
 * @version 1.0
 * @license GPL v2
 */
class EnvatoController extends EnvatoAuthController
{

    private $personal_token;
    private $refreshToken;
    private $api_url   = "https://api.envato.com/v3/market";
    private $download_url;
    private $mandatory = true;

    public function setPersonalToken()
    {
        $user = \App\Plugins\Envato\Model\Envato::where('access_token','!=','')
                ->first();
        if (!$user) {
            throw new \Exception("Author hasn't authenticated by Envato");
        }
        $this->personal_token = $user->access_token;
        $this->refreshToken = $user->refresh_token;
        return $this;
    }
    public function verifyPurchaseCode($code)
    {
        $url = $this->api_url . "/author/sale?code=" . $code;
        $x   = $this->request($url);
        return $x;
    }
    public function request($url, $decode_json = false)
    {
        $client = new \GuzzleHttp\Client();
        try {
            $data  = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => "Bearer " . $this->personal_token,
                ],
            ]);
            $data->getHeaderLine('content-type');
            $json  = $data->getBody()->getContents();
            $array = json_decode($json, true);
            return $array;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents());
            if ($e->getCode() == 404) {
                if ($this->mandatory) {
                    throw new \Exception($response->description);
                }
            } elseif ($e->getCode() == 401) {
                throw new \Exception("Author hasn't authenticated by Envato");
            } elseif ($e->getCode() == 403) {
                $this->getAccessToken($this->refreshToken, 'refresh_token');
                $this->setPersonalToken()->request($url);
            }
        }
    }
    public function setMandatory($mandatory = true)
    {
        $this->mandatory = $mandatory;
        return $this;
    }
}
