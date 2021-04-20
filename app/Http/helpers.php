<?php

use App\Model\helpdesk\Settings\Plugin;

/**
 * log the actions in log files
 * @param string $context
 * @param string $message
 * @param string $level
 * @param array $array
 */
function loging($context, $message, $level = 'error', $array = [])
{

    \Log::$level($message . ":-:-:-" . $context, $array);
}
/**
 * find a key exists in an array
 * @param string $key
 * @param array $array
 * @return string
 */
function checkArray($key, $array)
{
    $value = "";
    if (is_array($array) && array_key_exists($key, $array)) {
        $value = $array[$key];
    }
    return $value;
}
/**
 * get the image type
 * @param string $type
 * @return string
 */
function mime($type)
{
    if ($type == 'jpg' ||
            $type == 'png' ||
            $type == 'PNG' ||
            $type == 'JPG' ||
            $type == 'jpeg' ||
            $type == 'JPEG' ||
            $type == 'gif' ||
            $type == 'GIF' ||
            $type == 'image/jpeg' ||
            $type == 'image/jpg' ||
            $type == 'image/gif' ||
            $type == "application/octet-stream" ||
            $type == "image/png" ||
            starts_with($type, 'image')) {
        return "image";
    }
}

/**
 * check bill module enables
 * @return boolean
 */
function isBill()
{
    $check = false;
    if (\Schema::hasTable('common_settings')) {
        $settings = \DB::table('common_settings')->where('option_name', 'bill')->first();
        if ($settings && $settings->status == 1) {
            $check = true;
        }
    }
    return $check;
}

/**
 * check time track module enables
 * @return boolean
 */
function isTimeTrack()
{
    $check = false;
    if (\Schema::hasTable('common_settings')) {
        $settings = \DB::table('common_settings')->where('option_name', 'time_track')->first();
        if ($settings && $settings->status == 1) {
            $check = true;
        }
    }
    return $check;
}

/**
 * check cron url on/off
 * @return boolean
 */
function isCronUrl()
{
    $check    = false;
    $settings = \DB::table('common_settings')->where('option_name', 'cron_url')->first();
    if ($settings && $settings->status == 1) {
        $check = true;
    }

    return $check;
}
/**
 * render the html for delete popup
 * @param mixed $id
 * @param string $url
 * @param string $title
 * @param string $class
 * @param string $btn_name
 * @param string $button_check
 * @return string
 */
function deletePopUp($id, $url, $title = "Delete", $class = "btn btn-primary btn-xs", $btn_name
= "Delete", $button_check = true, $methodName = 'get')
{
    $button = "";
    if ($button_check == true) {
        $button = '<a href="#delete" class="' . $class . '" data-toggle="modal" data-target="#delete' . $id . '"><i class="fas fa-trash" style="color:white;">&nbsp;</i>' . $btn_name . '</a>';
    }
    return $button . '<div class="modal fade" id="delete' . $id . '">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">' . $title . '</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                
                                <span>Are you sure ?</span>
                                
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" id="close" class="btn btn-default" data-dismiss="modal"><i class="fas fa-times" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('lang.close').'</button>
                                <a id="delete-button" href="javascript:void(0)" class="btn btn-danger"
                                onclick="clickAndDisable(this, '.'\''.$url.'\''.', '.'\''.csrf_token().'\''.', '.'\''.$methodName.'\''.');">
                                <i class="fas fa-trash">&nbsp;&nbsp;</i>'.Lang::get('lang.delete').'</a>
                            </div>
                        </div>
                    </div>
                </div><script>

               function clickAndDisable(link, url, token, methodName) {

                 // disable subsequent clicks
                 link.onclick = function(event) {
                    event.preventDefault();
                 }

                 if(methodName == "delete"){
                   $.ajax({
                       type: methodName,
                       url: url,
                       data: {
                           "_token": token,
                       },
                       success: function() {
                         window.location.reload();
                       },
                       error: function() {
                         window.location.reload();
                       }
                   });
                 } else {
                   window.location.href = url;
                 }
               }
</script>';
}
/**
 * check installed
 * @return boolean
 */
function isInstall()
{
    $check = false;
    $env   = base_path('.env');
    if (\Config::get('database.install') == 1) {
        $check = true;
    }
    return $check;
}
/**
 *
 * @param string $date
 * @param integer $hour
 * @param integer $min
 * @param integer $sec
 * @param string $tz
 * @return \Carbon\Carbon
 */
function faveotime($date, $hour = 0, $min = 0, $sec = 0, $tz = "")
{
    if (is_bool($hour) && $hour == true) {
        $hour = $date->hour;
    }
    if (is_bool($min) && $min == true) {
        $min = $date->minute;
    }
    if (is_bool($sec) && $sec == true) {
        $sec = $date->second;
    }
    if (!$tz) {
        $tz = timezone();
    }
    $date1 = \Carbon\Carbon::create($date->year, $date->month, $date->day, $hour, $min, $sec, $tz);
    return $date1->hour($hour)->minute($min)->second($sec);
}

/**
 * get carbon instance
 * @param string $date
 * @param string $glue
 * @param string $format
 * @param boolean $flag
 * @return \Carbon\Carbon
 */
function getCarbon($date, $glue = '-', $format = "Y-m-d", $flag = true)
{
    //dd($date,$glue);
    $parse = explode($glue, $date);
    if ($format == "Y-m-d") {
        $day   = $parse[2];
        $month = $parse[1];
        $year  = $parse[0];
    }

    if ($format == "m-d-Y") {
        $month = $parse[0];
        $day   = $parse[1];
        $year  = $parse[2];
    }

    $hour   = 0;
    $minute = 0;
    $second = 0;
    if ($format == "Y-m-d H:m:i") {
        $day   = $parse[2];
        $month = $parse[1];
        $year  = $parse[0];
    }
    if (!$flag) {
        $hour   = 23;
        $minute = 59;
        $second = 59;
    }
    $carbon = \Carbon\Carbon::create($year, $month, $day, $hour, $minute, $second);
    return $carbon;
}
/**
 * create carbon instance
 * @param string $date
 * @param string $tz
 * @param string $format
 * @return \Carbon\Carbon
 */
function createCarbon($date, $tz = "", $format = "")
{
    if (!$tz) {
        $tz = timezone();
    }
    if (!$format) {
        $format = dateTimeFormat();
    }
    return \Carbon\Carbon::parse($date)->tz($tz)->format($format);
}
/**
 * parse the carbon
 * @param string $date
 * @return \Carbon\Carbon
 */
function carbon($date)
{
    return \Carbon\Carbon::parse($date);
}
/**
 * get the priority from sla
 * @param integer $slaid
 * @return integer
 */
function priority($slaid)
{
    if ($slaid) {
        $sla         = new App\Http\Controllers\SLA\Sla($slaid);
        $priority_id = $sla->priority;
        return $priority_id;
    }
    else {
        $priority = App\Model\helpdesk\Ticket\Ticket_Priority::select('priority_id')->first();
        return $priority->priority_id;
    }
}
/**
 * get system timezone
 * @return string
 */
function timezone()
{
    if (\Auth::check()) {
        $auth_timezone = \Auth::user()->agent_tzone;
        if ($auth_timezone && !is_numeric($auth_timezone)) {
            return \Auth::user()->agent_tzone;
        }
    }
    $system = App\Model\helpdesk\Settings\System::first();
    $tz     = "UTC";
    if ($system) {
        $tz = $system->time_zone;
    }
    return $tz;
}

/**
 * get system date-time format
 * @param bool $momentFormat
 * @return string
 */
function dateTimeFormat($momentFormat = false)
{
    return Cache::remember('date_time_format_'.$momentFormat, 10, function() use($momentFormat) {
        $dateFormat = App\Model\helpdesk\Settings\System::value('date_format');
        $timeFormat = App\Model\helpdesk\Settings\System::value('time_format');
        $dateTimeFormat = $dateFormat." ".$timeFormat;
        
        if($momentFormat){
            $dateTimeFormat = str_replace('d', 'DD', $dateTimeFormat);
            $dateTimeFormat = str_replace('m', 'MM', $dateTimeFormat);
            $dateTimeFormat = str_replace('i', 'mm', $dateTimeFormat);
            $dateTimeFormat = str_replace('s', 'ss', $dateTimeFormat);
            $dateTimeFormat = str_replace('H', 'HH', $dateTimeFormat);
            $dateTimeFormat = str_replace('g', 'hh', $dateTimeFormat);
            $dateTimeFormat = str_replace('i', 'mm', $dateTimeFormat);
            $dateTimeFormat = str_replace('F', 'MMMM', $dateTimeFormat);
            $dateTimeFormat = str_replace('d', 'DD', $dateTimeFormat);
            $dateTimeFormat = str_replace('j', 'DD', $dateTimeFormat);
        }
        
        return $dateTimeFormat;
    });
}

/**
 * get system time format
 * @return string
 */
function dateFormat()
{
    return Cache::remember('date_format', 10, function() {
        return App\Model\helpdesk\Settings\System::value('date_format');
    });
}

/**
 * generate url with application url
 * @param string $route
 * @return string
 */
function faveoUrl($route)
{
    $url    = \Config::get('app.url');
    if (!str_finish($url, '/')) {
        $url = $url . "/";
    }
    //dd($url."/".$route);
    return $url . "/" . $route;
}
/**
 * @category function to UTF encoding
 * @param string name
 * @return string name
 */
function utfEncoding($name)
{
    $title = "";
    $array = imap_mime_header_decode($name);
    if (is_array($array) && count($array) > 0) {
        if ($array[0]->charset != 'UTF-8') {
            $name = imap_utf8($name);
        }
        else {
            foreach ($array as $text) {
                $title .= $text->text;
            }
            $name = $title;
        }
    }
    if ((ord($name) >= 97 && ord($name) <= 122) || (ord($name) >= 65 && ord($name)
            <= 90)) {
        $name = ucfirst($name);
    }
    return $name;
}
/**
 * get the role of the user
 * @param integer $id
 * @return string
 */
function role($id)
{
    $user = \App\User::where('id', $id)->select('role')->first();
    if ($user) {
        return $user->role;
    }
}
/**
 * get the ticket title
 * @param integer $ticketid
 * @return string
 */
function title($ticketid)
{
    $thread = firstThread($ticketid);
    if ($thread) {
        return utfEncoding($thread->title);
    }
}
/**
 * get the requester of the ticket
 * @param integer $ticketid
 * @return integer
 */
function requester($ticketid)
{
    $ticket = ticket($ticketid);
    if ($ticket) {
        return $ticket->user_id;
    }
}
/**
 * get the thread
 * @param integer $threadid
 * @return \App\Model\helpdesk\Utility\Ticket_thread
 */
function thread($threadid)
{
    return App\Model\helpdesk\Utility\Ticket_thread::where('id', $threadid)
                    ->select('title', 'user_id', 'id', 'poster', 'is_internal')
                    ->first();
}
/**
 * get the poster
 * @param integer $threadid
 * @return string
 */
function poster($threadid)
{
    $thread = thread($threadid);
    if ($thread) {
        return $thread->poster;
    }
}

/**
 * get the ticket
 * @param integer $ticketid
 * @return \App\Model\helpdesk\Ticket\Tickets
 */
function ticket($ticketid)
{
    return App\Model\helpdesk\Ticket\Tickets::where('id', $ticketid)
                    ->select('user_id', 'assigned_to', 'sla', 'priority_id', 'dept_id', 'source', 'duedate')
                    ->first();
}
/**
 * get the first thread of the ticket
 * @param integer $ticketid
 * @return \App\Model\helpdesk\Utility\Ticket_thread
 */
function firstThread($ticketid)
{
    return App\Model\helpdesk\Utility\Ticket_thread::where('ticket_id', $ticketid)
                    ->whereNotNull('title')
                    ->where('title', '!=', '')
                    ->select('title', 'user_id', 'id', 'poster', 'is_internal')
                    ->orderBy('id')->first();
}

/**
 * get the default source
 * @param integer $ticketid
 * @return string
 */
function source($ticketid)
{
    $ticket = ticket($ticketid);
    if ($ticket) {
        return $ticket->source;
    }
}

/**
 * convert the string to hours
 * @param string $time
 * @param string $format
 * @return string
 */
function convertToHours($time, $format = '%02d:%02d')
{
    if ($time < 1) {
        return;
    }
    $hours   = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}
/**
 * collapse the array
 * @param array $array
 * @return array
 */
function collapse($array)
{
    $arrays = [];
    if (count($array) > 0) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if (!is_array($v)) {
                        $arrays[$k] = $v;
                    }
                }
            }
        }
    }
    return $arrays;
}
/**
 * delete the files
 * @param string $dir
 * @return mixed
 */
function delTree($dir)
{
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

/**
 * return date into string format according to default timezone and format
 * @param mixed $date
 * @param string $format
 * @param string $tz
 * @return string
 */
function faveoDate($date = "", $format = "", $tz = "", $change_tz=true)
{
    if (!$date) {
        $date = \Carbon\Carbon::now();
    }
    if (!is_object($date)) {
        $date = carbon($date);
    }
    if (!$format) {
        $format = dateTimeFormat();
    }
    if (!$tz) {
        $tz = timezone();
    }
    try {
        if($change_tz) {
            if ($format == "human-read") {
                return $date->tz($tz)->diffForHumans();
            }
            return $date->tz($tz)->format($format);
        } else {
            if ($format == "human-read") {
                return $date->diffForHumans();
            }
            return $date->format($format);
        }
    } catch (\Exception $ex) {
        return "invalid";
    }
}
/**
 * get the domain
 * @return string
 */
function domainUrl()
{
    return sprintf(
            "%s://%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https'
                        : 'http', $_SERVER['SERVER_NAME']
    );
}
/**
 * get the ticket number of a ticket
 * @param integer $ticketid
 * @return integer
 */
function ticketNumber($ticketid)
{
    return App\Model\helpdesk\Ticket\Tickets::where('id', $ticketid)
                    ->select('ticket_number')
                    ->value('ticket_number');
}
/**
 * check a plugin is on/off
 * @param string $plugin
 * @return boolean
 */
function isPlugin($plugin = 'ServiceDesk')
{
    $plugin = \DB::table('plugins')->where('name', $plugin)->where('status', 1)->count();
    $check  = false;
    if ($plugin > 0) {
        $check = true;
    }
    return $check;
}
/**
 * get the default storage
 * @return string
 */
function storageDrive()
{
    $drive    = 'local';
    $settings = \DB::table('common_settings')->where('option_name', 'storage')
                    ->where('optional_field', 'default')->first();
    if ($settings && $settings->option_value) {
        $drive = $settings->option_value;
    }
    return $drive;
}
/**
 * get maximum file upload size
 * @staticvar integer $max_size
 * @return integer
 */
function file_upload_max_size()
{
    static $max_size = -1;

    if ($max_size < 0) {
        // Start with post_max_size.
        $max_size = parse_size(ini_get('post_max_size'));

        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $upload_max = parse_size(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return ($max_size / 1024) / 1024;
}
function parse_size($size)
{
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
        // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    }
    else {
        return round($size);
    }
}
/**
 * @category funcion to get the value of account activation method
 * @param object of model CommonSettings : $settings
 * @var string $value
 * @return string $value: value of activation option fetched from DB
 */
function getAccountActivationOptionValue()
{
    $value = App\Model\helpdesk\Settings\CommonSettings::select('option_value')->where('option_name', '=', 'login_restrictions')->first();
    return $value->option_value;
}
/**
 * @category Funcion to set validation rule for email
 * @param null
 * @return string : validation rule
 */
function getEmailValidation()
{
    $value           = getAccountActivationOptionValue();
    $email_mandatory = App\Model\helpdesk\Settings\CommonSettings::select('status')->where('option_name', '=', 'email_mandatory')->first();
    if ($value == 'email' || $value == 'email,mobile' || $email_mandatory->status
            == 1) {
        return 'required|max:50|email|unique:users,email';
    }
    else {
        return 'max:50|email|unique:users,email';
    }
}
/**
 * @category Funcion to set validation rule for mobile and code
 * @param string $field : name of field
 * @return string : validation rule
 */
function getMobileValidation($field)
{
    $value = getAccountActivationOptionValue();
    if (strpos($value, 'mobile') !== false) {
        return 'required|numeric|max:999999999999999|unique:users,mobile';
    }
    else {
        return 'numeric|max:999999999999999|unique:users,mobile';
    }
}
/**
 * get default department id
 * @return integer
 */
function defaultDepartmentId()
{
    $id     = "";
    $system = \App\Model\helpdesk\Settings\System::select('department')->first();
    if ($system) {
        $id = $system->department;
    }
    else {
        $department = App\Model\helpdesk\Agent\Department::select('id')->first();
        $id         = $department->id;
    }
    return $id;
}
function isMicroOrg()
{
    $check = false;
    if (\Schema::hasTable('common_settings')) {
        $settings = \DB::table('common_settings')->where('option_name', 'micro_organization_status')->first();
        if ($settings && $settings->status == 1) {
            $check = true;
        }
    }
    return $check;
}
/**
 * @category function to return array values if status id
 * @param string purpose of status
 * @param string $relation  if equal and not equal
 * @return array ids of status with purpose passed as string
 */
function getStatusArray($status, $relation = "=")
{
    return \App\Model\helpdesk\Ticket\Ticket_Status::whereHas('type', function($query) use ($status, $relation){
        $query->where('name', $relation, $status);
    })->pluck('id')->toArray();
}

function departmentByHelptopic($helptopic_id)
{
    $help_topic = \App\Model\helpdesk\Manage\Help_topic::where('id', '=', $helptopic_id)->select('department')->first();
    if ($help_topic) {
        $department_id = $help_topic->department;
    }
    else {
        $department_id = defaultDepartmentId();
    }
    return $department_id;
}
function commonSettings($option, $optionField, $returnColum = 'option_value')
{
    return \App\Model\helpdesk\Settings\CommonSettings::where('option_name', $option)
                    ->where('optional_field', $optionField)
                    ->value($returnColum);
}
/**
 * @category function to get GMT for system timezone
 * @param null
 * @var $system, $tz
 * @return string GMT value of timezone
 */
function getGMT($fetchId = false)
{
    $system = \App\Model\helpdesk\Settings\System::select('time_zone_id')->first();
    $tz     = \DB::table('timezone')->select('location', 'id')->where('name', '=', $system->time_zone)->first();
    if ($fetchId) {
        return $tz->id;
    }
    $tz = explode(")", substr($tz->location, stripos($tz->location, 'T') + 1));
    return $tz[0];
}

function apiSettings($key)
{
    return \App\Model\Api\ApiSetting::where('key', $key)->value('value');
}

// For API response
/**
 * Format the error message into json error response
 *
 * @param string|array $message Error message
 * @param int $statusCode
 * @return \Illuminate\Http\JsonResponse json response
 */
function errorResponse($message, $statusCode = FAVEO_ERROR_CODE) {
    /**
     * When developers simply want to send info of exceptions they are handling 
     * using errorResponse() and explicitly pass $e->getCode() as second argument
     * then if thrown exception has code as 0 then response() will throw an error
     * and response will be modified to new exception. We are handling this here
     * so that developers can easily use this method and simply pass $e->getCode()
     * to use for response HTTP code.
     */
    $statusCode = ($statusCode)?:FAVEO_ERROR_CODE;
    return response()->json(['success' => false, 'message' => $message], $statusCode);
}

/**
 * Format success message/data into json success response
 *
 * @param string $message Success message
 * @param array|string $data Data of the response
 * @param int $statusCode
 * @return \Illuminate\Http\JsonResponse json response
 */
function successResponse($message = '', $data = '', $statusCode = FAVEO_SUCCESS_CODE) {
    $response = ['success' => true];

    // if message given
    if (!empty($message)) {
        $response['message'] = $message;
    }

    // If data given
    if (!empty($data)) {
        $response['data'] = $data;
    }

    return response()->json($response, $statusCode);
}



/**
 * Format exception response with exception details
 *
 * @param \Exception $exception Exception instance
 * @return \Illuminate\Http\JsonResponse json response
 */
function exceptionResponse(\Exception $exception){
    return response()->json(
        [
            'success' => false,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(). " (".$exception->getLine().")",
            'trace' => $exception->getTraceAsString()
        ], FAVEO_EXCEPTION_CODE);
}

/**
 * gets logged in agent's timezone. If a guest accesses this, system's timezone will be returned
 */
function agentTimeZone($user = null)
{
    $user = $user ? : Auth::user();

    try{
        // this cache will be invalidated in 5 seconds.
        // NOTE FROM AVINASH: there are places where we are using this function in iteration,
        // which is causing multiple DB calls. It would do fine for at-least consecutive API calls
        return Cache::remember('timezone_'.$user->id,5, function() use ($user){
            $timezone = null;

            if($user && $user->role != 'user'){
                $timezone = \App\Model\kb\Timezone::where('id', $user->agent_tzone)->value('name');
            }

            if(!$timezone){
                $timezone = \App\Model\helpdesk\Settings\System::first()->time_zone;
            }

            return $timezone;
        });
    }
    catch(\Exception $e){
        return \App\Model\helpdesk\Settings\System::first()->time_zone;
    }
}

/**
 * converts datetime from one timezone to another
 * @param string $dateTime         dateTime in given timzone
 * @param string $fromTimezone     timezone from which date has to e changed
 * @param string $toTimezone       timezone to which date has to be changed
 * @return string           dateTime in changed timezone
 */
function changeTimezoneForDatetime($dateTime,$fromTimezone, $toTimezone)
{
    $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dateTime, $fromTimezone);
    return $date->setTimezone($toTimezone);
}


/**
 * sets mail config and reloads the config into the container
 * NOTE: this is getting used outside the class to set service config
 * @return void
 */
function setServiceConfig($emailConfig)
{
    $sendingProtocol = $emailConfig->sending_protocol;
    if($sendingProtocol && $sendingProtocol != 'smtp' && $sendingProtocol != 'mail' && $sendingProtocol != 'ews'){

        $services = \Config::get("services.$sendingProtocol");
        $dynamicServiceConfig = [];

        //loop over it and assign according to the keys given by user
        foreach($services as $key => $value){
            $dynamicServiceConfig[$key] = isset($emailConfig[$key]) ? $emailConfig[$key] : $value;
        }

        //setting that service configuration
        \Config::set("services.$sendingProtocol", $dynamicServiceConfig);
    }
    else {
        \Config::set('mail.host',$emailConfig['sending_host']);
        \Config::set('mail.port', $emailConfig['sending_port']);
        \Config::set('mail.password', $emailConfig['password']);
        \Config::set('mail.security', $emailConfig['sending_encryption']);
    }

    //setting mail driver as $sending protocol
    \Config::set('mail.driver', $sendingProtocol);
    \Config::set('mail.from.address', $emailConfig['email_address']);
    \Config::set('mail.from.name',  $emailConfig['email_name']);
    \Config::set('mail.username', $emailConfig['user_name']);

    //setting the config again in the service container
    (new \Illuminate\Mail\MailServiceProvider(app()))->register();
}

/**
     * upload to system
     * @param mixed $attachment
     * @param string $upload_path
     * @param string $filename
     */

function uploadInLocal($attachment, $upload_path, $filename)
    {
            if (!\File::exists($upload_path)) {
                \File::makeDirectory($upload_path, 0777, true);
            }
            $path = $upload_path . DIRECTORY_SEPARATOR . $filename;
            if (method_exists($attachment, 'getStructure')) {
                $attachment->saveAs($path);
            }
            else {
                $attachment->move($upload_path, $filename);
            }
}
/**
* This function return time zone name with GMT format  ex:-(GMT-11:00)Pacific/Midway
* @return type array
*/

function timezoneFormat(){

        $timezonesList = \App\Model\helpdesk\Utility\Timezones::orderBy('id','ASC')->get();

                         //
            foreach ($timezonesList as $timezone) {
            $location = $timezone->location;
            $start  = strpos($location, '(');
            $end    = strpos($location, ')', $start + 1);
            $length = $end - $start;
            $result = substr($location, $start + 1, $length - 1);
            $display[]=(['id'=>$timezone->id ,'name'=> '('.$result.')'.' '.$timezone->name]);
            }
             //for display
            return array_column($display,'name','id');

    }


    /**
     * Sets up DB config for testing
     * @param  string $dbUsername  mysql username
     * @param  string $dbPassword  mysql password
     * @return null
     */
    function setupConfig($dbUsername, $dbPassword,$port='',$dbengine='')
    {
        Config::set('app.env', 'development');
        Config::set('database.connections.mysql.port', '');
        Config::set('database.connections.mysql.database', null);
        Config::set('database.connections.mysql.username', $dbUsername);
        Config::set('database.connections.mysql.password', $dbPassword);
        Config::set('database.connections.mysql.engine', $dbengine);
        Config::set('database.install', 0);
    }


/**
 * This function return active agent/admin list with alphabetic order (based on first name)
 * @param string $searchQuery
 * @return type array
 * @deprecated since 27/10/2020 God bless this innocent soul. RIP
 * @todo remove this method permanently after next two releases after current date
 * (perform cremation ceremony)
 */
function agentListAlphabeticOrder(array $userId = [], $searchQuery = NULL) {

    $agentLists = \App\User::where('is_delete', 0)->where('active', 1)-> where(function($query) use($userId) {
        $query->whereIn('id', $userId)->orwhere('role', 'admin');
        })->where(function($q) use($searchQuery) {
        $q -> where('first_name', 'LIKE', '%'.$searchQuery.'%')
           -> orWhere('user_name', 'LIKE', '%'.$searchQuery.'%')
           -> orWhere('email', 'LIKE', '%'.$searchQuery.'%');
        })-> select('id', 'first_name', 'last_name', 'user_name', 'email', 'user_name', 'role', 'profile_pic')->orderBy('first_name')->take(10)->get();

    $outPutData = [];

    foreach($agentLists as $agentList) {
        $outPutData[] = ['id' => $agentList->id, 'first_name' => $agentList->first_name, 'last_name' => $agentList->last_name, 'email' => $agentList->email, 'user_name' => $agentList->user_name, 'name' => $agentList->MetaName, 'role' => $agentList->role, 'profile_pic' => $agentList->profile_pic];
    }

    return $outPutData;
}

/**
* This function return agent list with based on permission
* @return type array
*/

function getAgentBasedOnPermission(string $permission) : array{
    $agentIds = \App\User::whereHas('permissions', function($query) use($permission) {
        $query->where('key', $permission);
    })
    ->where('role', '!=', 'user')
    ->pluck('id')->toArray();
    
    return $agentIds;
}
/**
 *========================================================================
 *                   Temporary functions block ends here
 *========================================================================
 */

/**
 * Creates an empty DB with given name
 * @param string $dbName    name of the DB
 * @return null
 */
function createDB(string $dbName)
{
    try{
         \DB::purge('mysql');
    // removing old db
    \DB::connection('mysql')->getPdo()->exec("DROP DATABASE IF EXISTS `{$dbName}`");

    // Creating testing_db
    \DB::connection('mysql')->getPdo()->exec("CREATE DATABASE `{$dbName}`");
    //disconnecting it will remove database config from the memory so that new database name can be
    // populated
    \DB::disconnect('mysql');
    }
    catch (\Exception $e) {
        return redirect()->back()->with('fails',$e->getMessage());
    }

}


/**
 * Accepts value in bytes and returns as string
 * for eg. 1024 will return 1 KB
 * @param  int $size
 * @return string
 */
function getSize($size) : string
{
  $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
  $power = $size > 0 ? floor(log($size, 1024)) : 0;
  $value = number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
  return $value;
}

/**
 * Checks if organization department modules is enabled
 * @return boolean
 */
function isOrgDeptModuleEnabled()
{
  return (bool)\App\Model\helpdesk\Settings\CommonSettings::where('option_name','micro_organization_status')
        ->where('status', 1)->count();
}
/**
 * This function return asset link based on link.php settings
 * @param string $type
 * @param string $key
 * @return type
 */
function assetLink(string $type, string $key)
{
    // if request if language, it should append & language to it
    return asset(\Config::get('link.' . $type . '.' . $key));
}

/**
 * Gives bundle URL after appending version number to it
 * @param  string $url
 * @return string
 */
function bundleLink(string $url) : string
{
    $baseUrl = asset($url)."?version=".\Config::get('app.tags');

    // if call is for language file, we should append language too in the url
    // REASON: we are sending cache headers while sending language response, which will improve performance since browser
    // will cache it. But as soon as language changes, language in cache will be same and will cause conflicts
    // adding language to argument will cause browser to request fresh response as soon as langauge changes
    // appending all activated plugin names too with the URL, so that if a plugin is activated, it requests a new
    // language file
    if(strpos($url, "js/lang") !== false){
        $activatePlugins = implode(",",Plugin::where('status',1)->orderBy("id", "asc")->pluck('id')->toArray());
        $baseUrl = $baseUrl."&lang=".App::getLocale()."&active-plugins=".$activatePlugins;
    }

    return $baseUrl;
}

/**
 * checks if a user is a team lead
 * @param  int  $teamId
 * @param  int  $userId
 * @return boolean
 */
function isTeamLead($teamId, $userId)
{
    if(\App\Model\helpdesk\Agent\Teams::where('id',$teamId)->where('team_lead', $userId)->count()){
      return true;
    }

    return false;
}

/**
 * Checks if a user is a department manager
 * @param  int  $departmentId
 * @param  int  $userId
 * @return boolean
 */
function isDepartmentManager($departmentId, $userId)
{
    if(\App\Model\helpdesk\Agent\DepartmentAssignManager::where('department_id', $departmentId)
        ->where('manager_id',$userId)->count()){
      return true;
    }

    return false;
}

/**
 * Checks CDN settings
 * @return boolean
 */
function isCdnActive()
{

    //get cdn settings value
    $checkCdnSettings = App\Model\helpdesk\Settings\CommonSettings::where('option_name', 'cdn_settings')->value('option_value');

    return $checkCdnSettings ? true : false;
}

/**
 * Checks if a agent have view profile permission
 * @param  int  $userId
 * @return boolean
 */
function isViewUserProfile($userId) {
    $checkRole = \App\User::find($userId)->role;
    $authUser = auth()->user();

    if($authUser->id == $userId || $authUser->role == "admin" || $checkRole == "user") {
        return true;
    }

    if($authUser->role == "agent" && $checkRole == "admin") {
        return false;
    }


    $deptIds = \App\Model\helpdesk\Agent\DepartmentAssignAgents::where('agent_id',$userId)->pluck('department_id')->toArray();
    $managerId = \App\Model\helpdesk\Agent\DepartmentAssignManager::whereIn('department_id', $deptIds)->where('manager_id',$authUser->id)->count();

    if($managerId > 0) {
        return true;
    }

    $teamId = \App\Model\helpdesk\Agent\Assign_team_agent::where('agent_id',$userId)->pluck('team_id')->toArray();
    $teamLeads = \App\Model\helpdesk\Agent\Teams::whereIn('id',$teamId)->where('team_lead',$authUser->id)->count();

    if($teamLeads > 0) {
        return true;
    }

    return false;
}

/**
 * Get active queue
 *
 * @since v1.9.49
 * @author Abhishek Kumar Haith <abhishek.haith@ladybirdweb.com>
 */
if (!function_exists('getActiveQueue')) {
    function getActiveQueue() {
        $activeQueue = \App\Model\MailJob\QueueService::where('status', 1)->first(['short_name']);

        return $activeQueue->short_name;
    }
}

/**
 * Gets user's profile path as an anchor link
 * @param  User $user
 * @return string
 */
function userProfilePath(\App\User $user) : string
{
  $userProfilePath = Config::get('app.url').'/user'."/".$user->id;
  return "<a href=$userProfilePath>$user->full_name</a>";
}

  /**
   * gets value from an object based on dot notation.
   * For eg. in object {status: {id: 1, name: 'test'}}
   * @param  object $object
   * @param  string $dotNotation
   * @return int|string|array
   */
  function getValueByDotNotation($object, string $dotNotation)
  {
    // explode by dot
    $arrayOfObjectDepth = explode('.',$dotNotation);

    $value = $object;

    foreach ($arrayOfObjectDepth as $property) {
      if(isset($value->$property)){
        $value = $value->$property;
      } else {
        $value = null;
      }
    }

    return $value;
  }

  function middlewareResponse($message, $status)
  {
      if(strpos(Request::url(), "/api/")){
          return errorResponse($message, $status);
      }

      return redirect("/$status");
   
   }
   /**
   * gets value from an model object based on array value count
   * For eg. if $newArray = [1,1,2,2,2,3,3,3,3,4,4,4,4,4,5] output should be [4,3,2,1,5]
   * @param  modelObject $modelObject
   * @param  string $coloumnName
   * @return array
   */
  function getPrioritiesValueAsArray($modelObject,$coloumnName)
  {

    $newArray = $modelObject
                ->select($coloumnName, DB::raw('count(*) as count'))
                ->orderBy('count', 'desc')
                ->orderBy($coloumnName, 'asc')
                ->groupBy($coloumnName)
                ->pluck($coloumnName)->toArray();
    return $newArray;

  }

  function helpMessage(string $key)
  {
    $link = getHelplink($key);

    if($link){
    // check if whitelabel plugin is enabled
        return " (<a href='$link' target='_blank'>".\Lang::get("lang.click_here_to_know_more")."</a>)";
    }
    return "";
  }

  /**
   * Gets helplink by checking if whitelabel plugin is active
   * @param $key
   * @return mixed
   */
  function getHelplink($key)
  {
    if(!\Event::dispatch('helpdesk.apply.whitelabel')){
        // check for whitelabel plugin and give link if not activated
        return \Config::get("helplink.$key");
    }
  }


/**
 * method to convert date time to time stamp format in UTC timezone
 * @param $timeStamp (date in agent timezone)
 * @return $utcDateFormat (timeStamp in UTC timezone and of time stamp format)
 */
function convertDateTimeToUtc($timeStamp)
{
    $utcTimeStamp = $timeStamp ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', str_replace('+',' ',$timeStamp), agentTimeZone())->setTimezone('UTC')->toDateTimeString() : $timeStamp;

    return $utcTimeStamp;
}

/**
 * Stores value for just the current request/connection. Will be destroyed as soon as request/connection terminates
 * USE-CASE: there are cases where same queries/calculation happens multiple time in a request. Caching them permanently
 * can lead to unexpected results, as data can change in the database. Limiting it to a request gives us the benefit of
 *
 * WARNING: DO NOT USE IT FOR STORING DATA WHICH IS MORE IN SIZE. IT WILL INCREASE THE MEMORY CONSUMPTION. USE persistentCache FOR SUCH CASES.
 *
 * @internal BAD PERFORMANCE AT HIGH TRAFFIC AND GOOD PERFORMANCE AT LOW TRAFFIC
 * @param $key
 * @param Closure $closure
 * @param array $variables  it makes a key unique based on the parameter. For eg. we want to cache template for dept A and dept B,
 *                          now we can give key as 'department_template' and key as department ID, in this case for both departments, there
 *                          will be a different cache
 * @return mixed any serializable data
 */
function quickCache($key, Closure $closure, array $variables = [])
{
    $keySalt = json_encode($variables);
    return Cache::store("request")->rememberForever($key.$keySalt, $closure);
}

/**
 * Stores value for given time period in default queue driver
 * Will be highly useful where different workers want to use same data. For eg. email configuration or queue configuration
 *
 * @internal GOOD PERFORMANCE AT HIGH TRAFFIC AND BAD PERFORMANCE AT LOW TRAFFIC
 * @param $key
 * @param Closure $closure
 * @param int $noOfSeconds
 * @param array $variables it makes a key unique based on the parameter. For eg. we want to cache template for dept A and dept B,
 *                          now we can give key as 'department_template' and key as department ID, in this case for both departments, there
 *                          will be a different cache
 * @return mixed  any serializable data
 */
function persistentCache($key, Closure $closure, $noOfSeconds = 30, array $variables = [])
{
    $keySalt = json_encode($variables);
    return Cache::remember($key.$keySalt, $noOfSeconds, $closure);
}

/**
 * Gets data from quick cache
 * @param string $key
 * @param array $variables
 * @return mixed
 */
function getFromQuickCache(string $key, $variables = [])
{
    $keySalt = json_encode($variables);
    return Cache::store("request")->get($key.$keySalt);
}

   /**
    * Returns an array with the differences between $array1 and $array2
    * @param array $array1
    * @param array $array2
    * @return array
    */
    function array_diff_recursive($array1, $array2)
    {
        $result = [];

        foreach ($array1 as $key => $value) {
            if (!is_array($array2) || !array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $recursiveArrayDiff = array_diff_recursive($value, $array2[$key]);

                if (count($recursiveArrayDiff)) {
                    $result[$key] = $recursiveArrayDiff;
                }

                continue;
            }

            if ($value != $array2[$key]) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Check if white label plugin is enabled
     * @return boolean 
     */
    function isWhiteLabelEnabled()
    {
        return is_dir(dirname(__DIR__,1) . DIRECTORY_SEPARATOR . 'Whitelabel');
    }
