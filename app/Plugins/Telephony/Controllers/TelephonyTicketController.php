<?php
namespace App\Plugins\Telephony\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\SyncPluginToLatestVersion;
use App\Plugins\Telephony\Traits\TicketHandler;
use App\Plugins\Telephony\Events\IncomingCallEvent;
use App\Plugins\Telephony\Model\TelephonyLog;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\Telephony\Model\TelephonyProvider;
use Illuminate\Http\Request;
use Logger;
use App\User;

/**
 * Class which provides generic implementation of call to ticket conversion
 * functionality.
 *
 * @author Manish Verma<manish.verma@ladybirdweb.com>
 * @package App\Plugins\Telephony\Controllers
 * @since v3.0.0
 */
class TelephonyTicketController extends Controller
{
	use TicketHandler;

	/**
     * Model id of department/helptopic posted in webhook URL to link call ticket
     * to department/helptopic
     * @var int
     */
    protected $modelId;
    
    /**
     * Model type of department/helptopic posted in webhook URL to link call ticket
     * to department/helptopic 
     * @var string
     */
    protected $model;

    /**
     * @var array 
     */
    protected $request;

    /**
     * @var App\Plugins\Telephony\Model\TelephonyProvider
     */
    protected $providerDetail;

    /**
     * @var string
     */
    protected $region = "IN";

	function __construct()
	{
		(new SyncPluginToLatestVersion)->sync('Telephony');
	}

    /**
     * Function to handle ticket conversion request and link call logs to tickets
     *
     * @param   TelephonyLog       call log for to link to a ticket
     * @param   Request            User request
     * @return  Illuminate\Http\JsonResponse
     */
    public function convertCallLogIntoTicket(TelephonyLog $callLog, Request $request, $autoConversion=false)
    {
        try {
            /**
             * In request user can either request to create new ticket (link_ticket=null) or
             * can send existing ticket id to link the call record.
             * But we will only allow linking
             * - if call log is not linked with any ticket then user can send any value
             * in link_ticket
             * - if call log is linked with a ticket then user must send id of that ticket as value
             * link_ticket
             * 
             * So in below condition we are checking subtraction of user given ticket id and linked
             * ticket id must be equal to 0 or user given ticket id itself.
             * 
             * case 1: call is not linked to any ticket so can be linked to new or given ticket
             * linked id = null, user given id = 5
             * so user given id - linked id = user given id (log will be linked to ticket id 5)
             *
             * linked id = null, user given id = null
             * so user given id - linked id = user given id (log will be linked to new ticket
             )                             
             *
             * case 2: call is already linked so it can not be linked to other ticket except the
             * same linked ticket
             * linked id = 5, user given id = 5
             * so user given id - linked id = 0 (log will be linked to existing linked ticket)
             *
             * linked id = 5, user given id = null
             * so user given id - linked id = -5 (log will not be linked)
             *
             * linked id = 5, user given id = 10
             * so user given id - linked id = 5 (log will not be linked)
             */
            if(!in_array($request->get('link_ticket',null)-$callLog->call_ticket_id, [0, $request->get('link_ticket',null)])) {
                return errorResponse(trans('Telephony::lang.call_already_linked_to_a_ticket'));
            }
            $this->callLog = $callLog->toArray();
            $this->caller = $this->getRequesterDetails();
            $this->agent = $this->assignTo($callLog->connecting_to);
            $ticketId = ($callLog->call_ticket_id)?:$this->handleTicket($request->get('link_ticket'))->id;
            $callLog->update([
                'call_ticket_id' => $ticketId,
                'notes' => $request->get('notes'),
                'auto_conversion' => $autoConversion
            ]);
            $this->callLog['notes'] = $request->get('notes', null);
            $this->addCallNotes($ticketId);
            return successResponse(trans('lang.added_successfully'));
        } catch (\Exception $e) {
            //Logging exceptions related to event broadcast silently
            Logger::exception($e);
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Function creates a new ticket with call data or links call log to given ticket
     * and returns ticket
     *
     * @param   string   id of a ticket as numeric string can be empty too 
     * @return  Tickets  newly linked or created ticket
     */
    private function handleTicket($ticketId=null):Tickets
    {
        return ($ticketId) ? $this->linkCallToTicket($ticketId) : $this->ticketConversion();
    }

    /**
     * Function to dispatch broadcasting event for showing call pop-up alerts.
     * It prepares payload to broadcast in event and dispatches the event with 
     * the payload which is then handled in event listener and then broadcasted on
     * socket channels.
     *
     * @param   array   Array containing required keys
     * @param   string  string to use as broadcasted event name (used in broadcastAs)
     * @param   bool    Tells front-end listener whether to allow ticket conversion action
     *                  on the event or not 
     * @return  void
     */
    private function handleAndDispatchPopUp(array $keysInRequest, Tickets $ticket = null, $broadCastAs = "call-ended", bool $allowedConversion):void
    {
        try {
            $callerDetail = $this->getCallerDetails($ticket);
            event(new IncomingCallEvent(
                $this->agent,
                $callerDetail,
                $broadCastAs,
                checkArray($keysInRequest[5], $this->request),
                checkArray($keysInRequest[0], $this->request),
                $this->providerDetail->conversion_waiting_time,
                $allowedConversion
            ));
        } catch (\Exception $e) {
            //Logging exceptions related to event broadcast silently
            Logger::exception($e);
        }
    }

    /**
     * Function handles call hooks for ticket conversion and dispatches broadcasting event
     * to which can be listen on front end to show different pop-ups
     *
     * @param  array    Array of minimal Required Keys coming in the request from different payload
     *                  of different IVR providers
     * @param  string   string to use as broad casted event name (used in broadcastAs) 
     * @param  bool     Decides two points
     *                  - whether to create call log record or not because for missed calls and call
     *                  start/end event we might only want to broadcast event to show pop-up in the
     *                  frontend but do not want to log the event as call record in db
     *                  - whether to create ticket or not
     *                  - tells whether the ticket conversion action can be taken during listening
     *                    the broadcasted event or not
     */
    protected function handleTicketConversion(array $keysInRequest, string $broadCastAs,bool $createLog=true):?Tickets
    {
        $callid = checkArray($keysInRequest[5], $this->request);
        $this->callLog = $this->initiateCallLog($callid, $keysInRequest, $createLog);
        $this->caller = $this->getRequesterDetails();
        $this->agent = $this->assignTo($this->callLog['connecting_to']);
        $ticket = null;
        $allowedConversion = false;
        if($createLog) {
            $this->saveCallDetails($callid, [
                'caller_user_id' => $this->caller['id'],
                'receiver_user_id' => $this->agent,
            ]);
            $broadCastAs = 'call-ended';
            $allowedConversion = true;
            if ($this->providerDetail->conversion_waiting_time == 0) {
                $ticket = $this->handleTicket(checkArray('call_ticket_id', $this->callLog));
                $this->saveCallDetails($callid, ['call_ticket_id' => $ticket->id]);
            }
        }
        $this->handleAndDispatchPopUp($keysInRequest, $ticket, $broadCastAs, $allowedConversion);

        return $ticket;
    }

    /**
     * Function initiates and returns call log array containing column names
     * as key and values as value. It will also create the log record in database 
     * if $createLog is true else it will just initiate and return the call log
     * array.
     *
     * @param   string   unique id of call record
     * @param   array    array containing keys to get values from request array
     * @param   boolean  decides whether to create save log in db or not
     * @return  array
     */
    private function initiateCallLog(string $callid, array $keys, bool $createLog=true):array
    {
        $parameters = [
            'call_from'   => checkArray($keys[0], $this->request),
            'call_to'     => checkArray($keys[1], $this->request),
            'connecting_to' => checkArray($keys[2], $this->request),
            'call_start_date'   => $this->formattedTime(checkArray(checkArray(3,$keys), $this->request)),
            'call_end_date'   => $this->formattedTime(checkArray(checkArray(4,$keys), $this->request)),
            'intended_department_id' => ($this->model=='department')? $this->modelId : null,
            'intended_helptopic_id' => ($this->model=='helptopic')? $this->modelId : null,
            'provider_id' => $this->providerDetail->id,
            'recording' => null
        ];
        if (count($keys)==7) {
            $parameters['recording'] = checkArray($keys[6], $this->request);
        }
        // $parameters = array_filter($parameters);
        return ($createLog) ? $this->saveCallDetails($callid, $parameters)->toArray() : $parameters;
    }

    /**
     * Fuction to create or update record in telephony_call_logs table to
     * store detail of call
     *
     * @param  string        $callId       unique id of the call
     * @param  array         $requestData  array containing column name as key and thei values
     *                                     as value for telephony_call_logs table
     * @return TelephonyLog                newly created or updated TelephonyLog
     */
    private function saveCallDetails(string $callid, array $requestData):TelephonyLog
    {
        return TelephonyLog::updateOrCreate([
            'call_id'=>$callid,
            'provider_id' => $this->providerDetail->id
        ], $requestData);
    }

    /**
     * Function returns formatted array of user containing user details and their
     * recent and linked ticket details
     *
     * @param   Tickets  $ticket  ticket which will be used as linked ticket
     * @return  array             formatted user details array
     */
    private function getCallerDetails(Tickets $ticket = null)
    {
        $linkedTicket= ($ticket) ? $this->fetchRequiredTicketDetails($ticket) : null;
        $recentTickets = null;
        if ($this->caller['id']) {
            $this->getRecentTicketOfUser($this->caller['id'], $recentTickets);
        }

        return array_merge([
            'linked_ticket' => $linkedTicket,
            'recent_tickets' => $recentTickets,
        ], $this->caller);
    }

    /**
     * Function updates $recentTickets and appends recently updated 5 ticket of
     * open status purpose of given user
     * 
     * @param   int  $userId         if of user to get recent tickets
     * @param        $recentTickets  reference to recentTickets so we can update it and
     *                              append recent ticket detail as its element                 
     * @return  void
     */
    private function getRecentTicketOfUser(int $userId, &$recentTickets):void
    {
        $tickets = Tickets::where('user_id', $userId)->whereIn('status', getStatusArray('open'))->orderBy('updated_at', 'desc')->take(5)->get();
        foreach ($tickets as $key => $ticket) {
            $recentTickets[$key] = $this->fetchRequiredTicketDetails($ticket);
        }
    }

    /**
     * Method to parse time string and return in UTC "YYYY-mm-dd H:i:s" format
     *
     * @param  string $time time string to be parsed
     * @return Mixed        if time string can be parsed then string UTC "YYYY-mm-dd H:i:s"
     *                      format else null  
     */
    private function formattedTime(string $time=null):?string
    {
        if(!is_numeric($time)){
            $time = strtotime($time);
        }

        return ($time) ? gmdate("Y-m-d H:i:s", $time) :null;
    }

    /**
     * Method simply prepares formatted array with ticket detail for given ticket 
     * @param   Tickets  $ticket  ticket to format details
     * @return  array             array containing details of the ticket
     */
    private function fetchRequiredTicketDetails(Tickets $ticket):array
    {
        return [
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'title' => $ticket->firstThread->title,
            'status' => $ticket->statuses->name,
            'status_icon' => $ticket->statuses->icon,
            'status_icon_color' => $ticket->statuses->icon_color,
            'priority' => $ticket->priority->priority,
            'priority_color' => $ticket->priority->priority_color,
            'created_at' => $ticket->created_at
        ];
    }
    
    /**
     * Method returns an array containing user details such as id, email, username
     * etc. If callLog contains number linked to existing user then it the resultant
     * array will contain details of that user else the array will be initialized with
     * null as different values such as id, email, name etc.
     *
     * @return  array  array containing user details
     */
    private function getRequesterDetails():array
    {
        $number = $this->formatNumber($this->callLog['call_from']);
        $existingUser = $this->searchUser($number);
        if($existingUser) {
            
            return [
                'id' => $existingUser->id, 'email' => $existingUser->email,
                'username' => $existingUser->user_name, 'phone' => $existingUser->phone,
                'country_code' => $existingUser->country_code, 'mobile' => $existingUser->mobile,
                'name' => $existingUser->name(), 'profile_pic' => $existingUser->profile_pic,
            ];
        }

        return [
            'id' => null, 'email' => null, 'username' => $number[1],
            'phone' => "", 'country_code' => $number[0], 'mobile' => $number[1],
            'name' => null, 'profile_pic' => null,
        ];
    }

    /**
     * Method searches for admin and agent who have given number as their mobile or
     * phone_number with country_code.
     *
     * @param  string  $mobile  phone number string eg: +918233077144, 08233077144, 8233077144
     * @return Mixed            integer id of agent or admin user having same number else null
     */
    private function assignTo(string $mobile):? int
    {
        $assigned = NULL;
        $number = $this->formatNumber($mobile);
        $agent = $this->searchUser($number, ['agent', 'admin']);
        if ($agent) {
            $assigned = $agent->id;
        }
        return $assigned;
    }

    /**
     * Methods searches user table for given number and role. Returns User if record with
     * given country code found in
     *
     * @param   array  $number  array containing country code and number
     * @param   array  $role    array containing allowed roles for searching 
     * @return  Mixed           User if found else null
     */
    private function searchUser(array $number, array $role=null):? User
    {
        return User::where('country_code', $number[0])->where(function($q) use($number){
            $q->where('mobile', $number[1])->orWhere('phone_number', $number[1]);
        })->when($role, function($q) use ($role) {
            $q->whereIn('role', $role);
        })->first();
    }

    /**
     * Method to parse given phone number in local format and separate country code
     * and phone number from number string. It utilizes PHP's libphonenumber library
     * for parsing the phone number so exceptions thrown via libphonenumber will be
     * thrown if error occurs during parsing the number.
     *
     * @param   string  $number  number to parse and format
     * @return  array            array containing country code and number as first and
     *                           second elements of the array.
     * @throws  \Exception
     */
    private function formatNumber(string $number):array
    {
        $number = trim($number);
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $parsedNumber = $phoneUtil->parse($number, $this->region);
        return [$parsedNumber->getCountryCode(), $parsedNumber->getNationalNumber()];
    }
}
