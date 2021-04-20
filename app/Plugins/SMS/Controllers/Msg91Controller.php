<?php

namespace App\Plugins\SMS\Controllers;

use App\Plugins\SMS\Model\Msg91;
use App\Plugins\SMS\Model\Provider;
use Exception;

class Msg91Controller {
    /*
      |--------------------------------------------------------------------------
      | Home Controller
      |--------------------------------------------------------------------------
      |
      | This controller renders your application's "dashboard" for users that
      | are authenticated. Of course, you are free to change or remove the
      | controller as you wish. It is just here to get your app started!
      |
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    static function sms($mobile, $msg) {
        try {
            // dd($mobile, $msg);
            $providers = Provider::select('id')->where('status', '=', 1)->first();
            if ($providers->id == 1) {
                $sms = new Msg91;
                $auth = $sms->where('name', 'auth_key')->first();
                $sender = $sms->where('name', 'sender_id')->first();
                $router = $sms->where('name', 'route')->first();
                //Your authentication key
                $authKey = $auth->value;
                //Multiple mobiles numbers separated by comma
                $mobileNumber = $mobile;
                //Sender ID,While using route4 sender id should be 6 characters long.
                $senderId = $sender->value;
                //Your message to send, Add URL encoding here.
                $message = strip_tags($msg);
                //Define route 
                $route = $router->value;
                //Prepare you post parameters
                $postData = [
                    'authkey' => $authKey,
                    'mobiles' => $mobileNumber,
                    'message' => urlencode($message),
                    'sender' => $senderId,
                    'route' => $route
                ];
                //API URL
                $url = "https://control.msg91.com/sendhttp.php";
            } else {
                $sms = new Msg91;
                $mobileNumber = $mobile;
                $owner_email = $sms->where('name', 'owner_email')->first()->value;
                $subacct = $sms->where('name', 'subacc')->first()->value;
                $subacctpwd = $sms->where('name', 'subacc_pass')->first()->value;
                $sender = $sms->where('name', 'smslive_sender_id')->first()->value;
                $postData = [
                    'cmd'        => 'login',
                    'owneremail' => $owner_email,
                    'subacct'    => $subacct,
                    'subacctpwd' => $subacctpwd,
                ];
                $url = "http://www.smslive247.com/http/index.aspx";
                $result = self::callCurl($postData, $url);
                if (strstr($result, 'ERR: ')) {
                    //do nothing 
                } else {
                    $sessionID = explode(" ",$result);
                    $msg = $msg;
                    $postData = [
                        'cmd'        => 'sendmsg',
                        'sessionid'  => $sessionID[1],
                        'message'    => urlencode($msg),
                        'sender'     => $sender,
                        'sendto'     => $mobileNumber,
                        'msgtype'    => 0,

                    ];
                }
            }
            // dd($postData);
            $result = self::callCurl($postData, $url);
            return $result;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function testConnection($authKey, $mobileNumber, $senderId, $route, $owner_email, $subacc, $subacc_pass, $smslive_sender_id) {
        try {
            if($authKey != null || $authKey != ''){
                $postData = array(
                    'authkey' => $authKey,
                    'mobiles' => $mobileNumber,
                    'message' => 'Testing',
                    'sender' => $senderId,
                    'route' => $route
                );
                //API URL
                $url = "https://control.msg91.com/sendhttp.php";
            } else {
                $postData = array(
                    'cmd' => 'sendquickmsg',
                    'owneremail' => $owner_email,
                    'subacct' => $subacc,
                    'subacctpwd' => $subacc_pass,
                    'message' => urlencode('testing').'%0D%0A',
                    'sender'  => $smslive_sender_id,
                    'sendto' => $mobileNumber,
                    'msgtype'   => 0,
                );
                $url = "http://www.smslive247.com/http/index.aspx";
            }
            $result = $this->callCurl($postData, $url);
            return $result;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     *@category function to call cURL and execute cURL
     *@param array $postData, string $url
     *@return string|boolean false $output
     *@author manish.verma@ladybirdweb.com 
     */
    static public function callCurl($postData, $url)
    {
            // init the resource
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData
                    //,CURLOPT_FOLLOWLOCATION => true
            ));
            //Ignore SSL certificate verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            //get response
            $output = curl_exec($ch);
            //Print error if any
            if (curl_errno($ch)) {
                echo 'error:' . curl_error($ch);
            }
            curl_close($ch);
            return $output;
    }
}
