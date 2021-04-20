<?php
namespace App\Plugins\Telephony\Controllers;

use Illuminate\Http\Request;
use App\Plugins\Telephony\Model\TelephonyProvider;
use App\Plugins\Telephony\Controllers\TelephonyTicketController;
use App\Model\helpdesk\Ticket\Tickets;
use Logger;
use Carbon\Carbon;

/**
 * Class handles webhook calls of different IVR providers and
 * process the request for call logging and converts calls into
 * tickets. 
 *
 * @author Manish Verma<manish.verma@ladybirdweb.com>
 * @since v3.0.0
 * @package App\Plugins\Telephony\Controllers
 */
class CallHookHandler extends TelephonyTicketController
{      
    /** 
     *=======================================================
     *      Class's Generic method block
     *=======================================================
     */

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct();
    }

    /**
     * Handler method for Webhook URL call. It transfers the controll to different IVR
     * hook handler messages to process the request and convert call to tickets.
     *
     * @param  string  $provider  provider short name
     * @param  int     $modelId   tells id of helptoic or department to which ticket belongs
     * @param  string  $model     tells whether ticket belongs to department or helptopic
     */
    public function handleHook(string $provider, int $modelId = null, string $model = null)
    {
        try {
            $methodName    = $provider."HookHandler";
            $this->modelId = $modelId;
            $this->model   = $model;
            $this->providerDetail = TelephonyProvider::where('short', $provider)->first();
            $this->region  = $this->providerDetail->iso;
            $this->request = array_change_key_case($this->request->all());
            $this->$methodName();

            return response('success', 200);
        } catch (\Exception $e) {
            Logger::exception($e);

            return response('failed', 500);
        }
    }

    /**
     * Fuction checks request for all required keys for different IVR providers.
     * Each provider provides same values with different keys and there are certain
     * key and there value which must be present in the request so we can proceed to
     * handle call to ticket conversion. 
     *
     * @param   array   $requiredKeys  Array containing keys which must be present
     *                                 in request. Values of $requiredKeys will be different
     *                                 for each IVR.
     * @param   string  $dataKey       if request has nested keys then $dataKey points
     *                                 to the key whose value is an array which must
     *                                 contain all required keys.
     * @return  bool                   true if all $requiredKeys are available in the
     *                                 request else false.
     */
    private function validateRequestArray($requiredKeys, string $dataKey = null):bool
    {
        if($dataKey) {
            $this->request = (array)checkArray($dataKey, $this->request);
            return $this->validateRequestArray($requiredKeys);
        }

        return !(bool)array_udiff($requiredKeys, array_keys(array_filter($this->request)), 'strcasecmp');
    }

    /**
     * Method validates webhook request call and proceeds the request for ticket
     * conversion.
     *
     * @param   array   $requiredKeys  array containing required keys to check in the request
     * @param   string  $dataKey       if request has nested array then the key which has
     *                                 array containing call details array
     * @param   bool    $createLog     tells system whether call should be logged for ticket
     *                                 conversion or not
     * @param   string  $broadCastAs   key to broadcast event for showing alert in frontend
     * @return  Mixed Tickets/null
     */
    private function validateRequestAndProceedToConvert(array $requiredKeys, string $dataKey = null, bool $createLog=true, string $broadCastAs = 'call-started'):?Tickets
    {
        $keysInRequest = $requiredKeys;
        if ($createLog && (bool)$this->providerDetail->log_miss_call) array_pop($requiredKeys);

        if (!$this->validateRequestArray($requiredKeys, $dataKey)) return null;

        return $this->handleTicketConversion($keysInRequest, $broadCastAs, $createLog);
    }
    /**====================Block Ends here====================**/

    /** 
     *=======================================================
     *                 Knowlarity block
     *=======================================================
     */

    /**
     * Method handles Knowlarity's webhook calls
     * @return void
     */
    private function knowlarityHookHandler() :void
    {
        // Sample request data for Knowrality
        // {
        //    "dispnumber":"+919019101000",
        //    "extension":"None",
        //    "callid":"ec8a4ba6-c2eb-4e71-888f-c3acbf585111",
        //    "call_duration":"7",
        //    "destination":"Welcome Sound",
        //    "caller_id":"+919966679850",
        //    "end_time":"2016-09-27 09:19:34.667036+05:30",
        //    "action":"6",
        //    "timezone":"Asia/Kolkata",
        //    "resource_url":"",
        //    "hangup_cause":"900",
        //    "type":"dtmf",
        //    "start_time":"2016-09-27 03:49:44.391601+00:00",
        //    "call_type":"incoming"
        // }
        $requiredKeys = ['caller_id', 'dispnumber', 'dispnumber', 'start_time', 'end_time', 'callid', 'resource_url'];
        $this->validateRequestAndProceedToConvert($requiredKeys);
    }
    /**====================Block Ends here====================**/

    /** 
     *=======================================================
     *                 MCube block
     *=======================================================
     */

    /**
     * Method handles MCube's webhook calls
     * @return void
     */
    private function mcubeHookHandler() :void
    {
        // Sample request data for mCube
        // {
           // "dispnumber":"+919019101000",
           // "extension":"None",
           // "callid":"ec8a4ba6-c2eb-4e71-888f-c3acbf585111",
           // "call_duration":"7",
           // "destination":"Welcome Sound",
           // "caller_id":"+919966679850",
           // "end_time":"2016-09-27 09:19:34.667036+05:30",
           // "action":"6",
           // "timezone":"Asia/Kolkata",
           // "resource_url":"",
           // "hangup_cause":"900",
           // "type":"dtmf",
           // "start_time":"2016-09-27 03:49:44.391601+00:00",
           // "call_type":"incoming"
        // }
        $requiredKeys = ['callfrom', 'empnumber', 'empnumber', 'start_time', 'end_time', 'callid', 'recordingUrl'];
        $this->validateRequestAndProceedToConvert($requiredKeys, 'data');
    }
    /**====================Block Ends here====================**/

    /** 
     *=======================================================
     *                 MyOperator block
     *=======================================================
     */

    /**
     * Method handles MyOperator's webhook calls
     * @return void
     */
    private function myoperatorHookHandler() :void
    {
        // Sample request data for MyOperator
        /**
         * MyOerator send complex JSON structured data while using POST hook
         * so it's required to instruct users to create GET hook which allows
         * to create custom key for required values. As for hook we have limited
         * data available we can guide users to add below parameters with same
         * key name
         */
        // {
        //     "caller_number"  : "+918764341282", (Caller number with country code)
        //     "caller_id"      : "das213dasd4d", (UID unique id for each call)
        //     "start_time"     : "1596500100", (start time of call epoch)
        //     "recieved_by"    : "+918233077144", (user's number)
        //     "recording"      : "32437246487226847367426478326478236qd3.mp3",( recording file name)
        // }
        $this->getMyOperatorRecording();
        $requiredKeys = ['caller_number', 'recieved_by', 'recieved_by', 'start_time', 'end_time', 'caller_id', 'recording'];
        $this->validateRequestAndProceedToConvert($requiredKeys);
    }

    /**
     * Method to add/update value of key "recording" in request array data.
     * If request has recording value then it converts that value to resource URL 
     * to access recording of call from MyOperator servers.
     *
     * @return void
     */
    private function getMyOperatorRecording():void
    {
        /**
         * As MyOperator does not allow accessing records without authentication we need
         * to make API call to get recording source file by passing token which was saved
         * in DB during configuration process in Faveo.
         */
        $token = $this->providerDetail->token;
        $this->request['recording'] = (checkArray('recording', $this->request)) ? "https://developers.myoperator.co/recordings?token=$token&file=".$this->request['recording']: "";
    }
    /**====================Block Ends here====================**/

    /** 
     *=======================================================
     *                 Exotel block
     *=======================================================
     */

    /**
     * Method handles Exotel's webhook calls
     * @return void
     */
    private function exotelHookHandler() :void
    {
        // Sample request data for Exotel
        // {
        //     "CallSid": "d95feaed6383fb24d71a6756126d5350",
        //     "From": "08042096073",
        //     "To": "08033172870",
        //     "Direction": "incoming",
        //     "DialCallDuration": "13",
        //     "StartTime": "2016-08-17 19:42:43",
        //     "EndTime": "0000-00-00 00:00:00",
        //     "CallType": "completed",
        //     "RecordingUrl": "https:\/\/s3-ap-southeast-1.amazonaws.com\/exotelrecordings\/laad5\/d95feaed6383fb24d71a6756126d5350.mp3",
        //     "DialWhomNumber": "09663218862",
        //     "Created": "Wed, 17 Aug 2016 19:42:43",
        //     "RecordingAvailableBy": "Wed, 17 Aug 2016 19:47:58",
        //     "flow_id": "107722",
        //     "tenant_id": "42758",
        //     "CallFrom": "08042096073",
        //     "CallTo": "08033172870",
        //     "DialCallStatus": "completed",
        //     "CurrentTime": "2016-08-17 19:42:58"
        // }
        $requiredKeys = ['from', 'to', 'dialwhomnumber', 'starttime', 'endtime', 'callsid', 'recordingurl'];
        $this->handleExotelTicketConversion($requiredKeys);
    }

    /**
     * Method processes Exotel's hook calls to handle different event calls 
     * of exotel and then send request for ticket conversion
     * 
     * @param  array  $requiredKeys  Array containing Exotel's generic required
     *                               keys to validate in wenhook request
     * @return  Mixed Tickets/null
     */
    private function handleExotelTicketConversion($requiredKeys):?Tickets
    {
        /**
         * If Status is missing means hook is not called by Popup alert so it should be
         * handled as normal hook call.
         */
        if(!$this->validateRequestArray(['status'])) {
            //Convert required time to UTC recieved in Passthrough.
            $this->convertExotelDateTimeToUTC($requiredKeys); 
            return $this->validateRequestAndProceedToConvert($requiredKeys);
        }
        /**
         * removing 'StartTime', 'EndTime' from $requiredKeys as they are not availble in popup
         * alert webhook payload
         */
        unset($requiredKeys[3],$requiredKeys[4]);
        //If Status is not busy means hook is called for call end information
        if(checkArray('status', $this->request) != 'busy')
            return $this->handleExotelCallEndLogging($requiredKeys);

        //process call start hook
        array_pop($requiredKeys);
        return $this->validateRequestAndProceedToConvert($requiredKeys, null, false);
    }

    /**
     * Method processes call ending hook call of Exotel and process them for ticket
     * conversion and event broadcasting.
     *
     * @param  array  $requiredKeys  Array containing Exotel's generic required
     *                               keys to validate in wenhook request
     * @return Mixed Tickets/null
     */
    private function handleExotelCallEndLogging($requiredKeys):?Tickets
    {
        /**
         * if hook contains "RecordingAvailableBy" means call is completed and
         * ticket conversion will be hanled by call completion hook
         */
        if($this->validateRequestArray(['RecordingAvailableBy'])) return null;
        
        /**
         * if log_miss_call is allowed we process the hook call as normal missed call
         * assuming we call completion hook will not be called.
         */
        $createLog = (bool)$this->providerDetail->log_miss_call;

        return $this->validateRequestAndProceedToConvert($requiredKeys, null, $createLog, 'call-ended');
    }

    /**
     * For some wierd reasons Exotel sends StartTime, EndTime in passthrough parameters in Asia/Kolkata
     * timezone leaving us to convert the required datetime to UTC so it can be saved and used by Faveo.
     * This method converts the required date time values into UTC in the request payload so it can be
     * used by Faveo.
     *
     * @param  array  $requiredKeys  Array containing Exotel's generic required
     *                               keys to validate in wenhook request
     * @link https://support.exotel.com/support/solutions/articles/48283-working-with-passthru-applet
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     * @since v3.0.0
     * @return void
     */
    private function convertExotelDateTimeToUTC(Array $keys):void
    {
        $this->request[$keys[3]] = Carbon::parse($this->request[$keys[3]], 'Asia/kolkata')->tz('UTC')->format('Y-m-d H:i:s');
        $this->request[$keys[4]] = Carbon::parse($this->request[$keys[4]], 'Asia/kolkata')->tz('UTC')->format('Y-m-d H:i:s');
    }
    /**====================Block Ends here====================**/
}
