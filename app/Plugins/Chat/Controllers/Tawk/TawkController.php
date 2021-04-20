<?php

namespace App\Plugins\Chat\Controllers\Tawk;

use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Manage\Tickettype;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Settings\Ticket;
use Logger;
use App\User;
use App\Plugins\Chat\Model\Chat;
use App\Http\Controllers\Controller;
use App\Plugins\Chat\Model\TawkChat;

class TawkController extends Controller
{
    /**
     * Department to generate ticket
     * @var  $request
     */
    private $department;

    /**
     * Helptopic to generate ticket
     * @var  $request
     */
    private $helptopic;

    /**
     * @var Request $request
     */
    private $request;

    /**
     * Email of the Requester
     * @var $email
     */
    private $email;

    /**
     * body from the Requester
     * @var $body
     */
    private $body;

    /**
     * subject from the Requester
     * @var $subject
     */
    private $subject;

    /**
     * chatId of the Requester
     * @var $chatId
     */
    private $chatId;


    /**
     * name of the Requester
     * @var $name
     */
    private $name;


    public function __construct($request, $department, $helpTopic)
    {
        $this->request = $request;

        $this->helptopic = Help_topic::where('id', $helpTopic)->count() ? $helpTopic : Ticket::value('help_topic');

        $this->department = Department::where('id', $department)->count() ? $department : $this->setDepartment();
    }

    /**
     * Handles setting department from helptopic/default when department configured in tawk is deleted
     *
     * @return mixed
     */
    private function setDepartment()
    {
        if ($departmentId = Help_topic::with('department')->where('id', $this->helptopic)->value('department')) {
            return $departmentId;
        }
        return System::value('department');
    }

    /**
     * Verify the request is from Tawk or not.
     * @param \Illuminate\Http\Request $request
     * @return Boolean
     */
    private function isThisRequestSecure()
    {
        $digest = hash_hmac('sha1', $this->request->getContent(), $this->getTawkSecret());
        return $this->request->header('X_TAWK_SIGNATURE') !== $digest ;
    }


    /**
     * Gets the Tawk Webhook Secret Key from database
     * @return mixed
     */
    private function getTawkSecret()
    {
        return Chat::where('short','tawk')->first()->secret_key;
        
    }

    /**
     * Twilio Webhook EntryPoint
     */
    public function webhookEntry()
    {
        if($this->isThisRequestSecure()){
            Logger::exception(
                new \Exception(trans('chat::lang.webhook_insecure'))
            );
            return errorResponse(trans('chat::lang.webhook_insecure'));
        }
        $request = $this->request->all();
        $event = $request['event'];
        $this->prepareDataBasedOnEvent($event);

    }

    /**
     * Prepares the data required to create ticket based on various events.
     * @param String $eventType
     */
    private function prepareDataBasedOnEvent($eventType)
    {
        switch ($eventType) {
            case "ticket:create":
                $this->prepareDataForTicketCreateEvent();
                break;
            case "chat:end":
                $this->prepareDataForChatEndEvent();
                break;
            case "chat:start":
                $this->persistChat();
                break;
        }
    }

    /**
     * Prepares data to create ticket when the ticket:create event is fired from Tawk.to
     *
     */
    private function prepareDataForTicketCreateEvent()
    {
        $request = $this->request->all();
        $this->email = $request['requester']['email'];
        $this->name  = $request['requester']['name'];
        $this->subject = $request['ticket']['subject'];
        $this->body = $request['ticket']['message'];
        $this->generateTicket();
    }


    /**
     * Prepares data to create ticket when the chat:end event is fired from Tawk.to
     *
     */
    private function prepareDataForChatEndEvent()
    {
        $request = $this->request->all();
        $this->chatId = $request['chatId'];
        $this->email  = isset($request['visitor']['email']) ? $request['visitor']['email'] : null;
        $this->prepareForTicketGeneration($this->chatId);
    }


    /**
     * Generates Ticket
     * @return array
     */
    private function generateTicket()
    {
        $ticketController = new TicketController();
        $priority = Ticket::find(1)->priority ?: '';
        $type = Tickettype::select('id')->first()->id ?: '';
        $user = $this->checkUser($this->email);
        $userName = ($user) ? $user['user_name'] : $this->email;
        $source = $ticketController->getSourceByname("Chat")->id;

        return $userName
            ? $ticketController->create_user(
                $userName,
                "",
                $this->subject,
                $this->body,
                "",
                "",
                "",
                $this->helptopic,
                '',
                $priority,
                $source,
                [],
                $this->department,
                null,
                [],
                "",
                "",
                $type,
                [],
                []
            )
            : [];
    }


    /**
     * Prepares data prior to ticket generation after chat end event trigger
     */
    private function prepareForTicketGeneration($chatId)
    {
        $chat = TawkChat::where('chat_id', $chatId)->first();
        if ($chat) {
            $this->body = $chat->body;
            $this->chatId = $chat->chat_id;
            $this->email = ($chat->from) ?: $this->email;
            $this->subject = "Message from Tawk.to";
            $this->generateTicket();
        }
    }


    /**
     * Checks whether the user exists in the system based on the email
     * From which the tawk message is recieved.
     * @param string $email
     * @return mixed
     */
    private function checkUser($email)
    {
        $user = User::where('email', $email);
        return ($user->first()) ? ($user->first()->toArray()) : '';
    }

    /**
     * Saves information about tawk message for further processing.
     * @param string $from
     * @param array $result
     */
    private function persistChat()
    {
        $request = $this->request->all();
        $tawk_chat = [
            'chat_id' => $request['chatId'],
            'body' => $request['message']['text']
        ];
        TawkChat::create($tawk_chat);
    }
}
