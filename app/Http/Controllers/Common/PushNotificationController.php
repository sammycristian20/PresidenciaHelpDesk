<?php

namespace App\Http\Controllers\Common;

use FCM;
use FCMGroup;
use App\User;
use Hashids\Hashids;
use Illuminate\Http\Request;
use LaravelFCM\Message\Topics;
use App\Http\Controllers\Controller;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Response\DownstreamResponse;
use App\Model\helpdesk\Notification\Subscribed;
use LaravelFCM\Message\PayloadNotificationBuilder;
use App\Model\helpdesk\Notification\PushNotification;

/**
 * **********************************************
 * PushNotificationController
 * **********************************************
 * This controller is used to send notification to FCM cloud which later will 
 * foreward notification to Mobile Application
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class PushNotificationController extends Controller {

    public function response($agent_token, $noti) {
//        dd(json_encode($noti));
        try {
            $requester_name = $noti['requester']['first_name']." ".$noti['requester']['last_name'];
            $message = checkArray('message', $noti);
            $optionBuiler = new OptionsBuilder();
            $optionBuiler->setTimeToLive(60 * 20);
            $optionBuiler->setPriority('high');
            $optionBuiler->setContentAvailable(true);
            $activity = "OPEN_ACTIVITY_1";
            if($noti['scenario']==='users'){
                $activity = "OPEN_ACTIVITY_2";
            }
            $notificationBuilder = new PayloadNotificationBuilder($requester_name);
            $notificationBuilder->setBody($message)
                    ->setSound('default')
                    ->setIcon('ic_stat_f1')
                    ->setClickAction($activity);

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData($noti);

            $option = $optionBuiler->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();
            $downstreamResponse = FCM::sendTo($agent_token, $option, $notification, $data);
            

//        $downstreamResponse = new DownstreamResponse($response, $tokens);
            //dd($downstreamResponse);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();

//return Array - you must remove all this tokens in your database
            $downstreamResponse->tokensToDelete();

//return Array (key : oldToken, value : new token - you must change the token in your database )
            $downstreamResponse->tokensToModify();

//return Array - you should try to resend the message to the tokens in the array
            $downstreamResponse->tokensToRetry();
        } catch (\Exception $ex) {
            loging('fcm', $ex->getMessage());
        }

// return Array (key:token, value:errror) - in production you should remove from your database the tokens
    }

    /**
     * function to get the fcm token from the api under a user.
     * @param \Illuminate\Http\Request $request
     * @return type
     */
    public function fcmToken(Request $request) {
        // return "success 123";
        // get the requested details 
        $user_id = $request->input('user_id');
        $fcm_token = $request->input('fcm_token');
        // check for all the valid details
        if ($user_id != null && $user_id != "" && $fcm_token != null && $fcm_token != "") {
            // search the user_id in database
            $user = User::where('id', '=', $user_id)->first();
            if ($user != null) {
                $user->notificationTokens()->updateOrCreate(['token' => $fcm_token],[
                    'token'  => $fcm_token,
                    'type'   => 'fcm_token',
                    'active' => 1
                ]);
                // success response for success case
                return ['response' => 'success'];
            } else {
                // failure respunse for invalid user_id in the system
                return ['response' => 'fail', 'reason' => 'Invalid user_id'];
            }
        } else {
            // failure respunse for invalid input credentials
            return ['response' => 'fail', 'reason' => 'Invalid Credentials'];
        }
    }

    public function subscribeUser(Request $request, Subscribed $subscribe){
        $hashids = new Hashids('', 10);
        $user_id = $hashids->decode($request->user);
        if($subscribed_users = $subscribe->where('user_id', $user_id[0])->where('browser_name',$request->browser_name)->first()){
            $subscribed_users->update([
                'user_id' => $user_id[0],
                'browser_name' => $request->browser_name,
                'version' => $request->version,
                'user_agent' => $request->user_agent,
                'platform' => $request->platform
            ]);
            return ["success" => true, "message" => "user subscription updated"];
        }
        else{
            $subscribe->create([
                'user_id' => $user_id[0],
                'browser_name' => $request->browser_name,
                'version' => $request->version,
                'user_agent' => $request->user_agent,
                'platform' => $request->platform
            ]);
            return ["success" => true, "message" => "subscribed successfully"];
        }

    }

    public function getSubscribedNotification(Request $request, PushNotification $notifications, Subscribed $subscribed){
        $hashids = new Hashids('', 10);
        $user_id = $hashids->decode($request->user);
        if($subscribed_user = $subscribed->where('user_id', $user_id[0])->where('browser_name', $request->browser_name)->first()){
            $message = $notifications->where('subscribed_user_id', $subscribed_user->id)->where('status', 'pending')->get()->take(2);
            return $message;
        }
        return $request->all();
    }

    public function notificationDelivered(PushNotification $notifications, $id){
        if($notify = $notifications->where('id', $id)->first()){
            $notify->update(['status' => 'delivered']);
            return ['success' => true, 'message' => 'successfully updated'];
        }
    }





}
