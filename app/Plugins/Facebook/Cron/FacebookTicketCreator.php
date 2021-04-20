<?php
namespace App\Plugins\Facebook\Cron;

use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Http\Controllers\Client\helpdesk\GuestController;
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Requests\helpdesk\AgentReplyRequest;
use App\Model\helpdesk\Manage\Tickettype;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\Facebook\Model\FacebookApp;
use App\Plugins\Facebook\Model\FbChannel;
use App\User;

class FacebookTicketCreator
{
    /*
     * @var $ticket_controller
     */
    private $ticketController;

    public function __construct()
    {
        //used by most methods of this class, so constructing
        $this->ticketController = new TicketController();
    }

    public function createTicket($fields, $provider = "facebook", $via = 'page_message', $page = "")
    {
        foreach ($fields as $field) {
            $email = $field['email'];
            $username = $field['name'];
            $userId = $field['user_id'];
            $body = ($field['message'])?:"\u{200C}"; //Invisible String; workaround for attachment without body (always in fb)
            $conversationId = $field['con_id']; //conversation id of the message for replying purpose
            $messageId = $field['message_id'];
            $name = $field['name'];
            $postedAt = $field['posted_at'];
            $attachments = $field['attachments'];

            if (!$this->messageIdExists($messageId)) {
                if ($this->checkReplyFacebookPageMsg($conversationId, $postedAt, $userId)) {
                    $result = $this->reply($body, $name, $conversationId, $attachments);
                    $this->prepareForPersist($field, $provider, $page, $result->ticket_id);
                } else {
                    $result = $this->ticketController->create_user(
                        $email,
                        $username,
                        'Facebook Message from ' . $name,
                        $body,
                        '',
                        '',
                        '',
                        Ticket::value('help_topic') ?: '',
                        '',
                        '',
                        $this->ticketController->getSourceByname($provider)->id,
                        [],
                        System::value('department'),
                        null,
                        [],
                        "",
                        "",
                        Tickettype::value('id') ?: '',
                        $attachments
                    );
                }
                if (is_array($result)) {
                    $this->insertInfo($field, $result, $provider, $page);
                }
            }
        }
    }

    /**
     * check whether conversation exists
     * @param string $message_id
     * @return boolean $check
     */
    private function messageIdExists($message_id)
    {
        return (bool)FbChannel::where('message_id', $message_id)->count();
    }

    /**
     * Checks whether conversation exist in the system or not
     * @param string $conversationId Conversation ID
     * @param string $postedAt Posted at Date & Time
     * @param string $userId Id of User
     * @return boolean $check
     */
    private function checkReplyFacebookPageMsg($conversationId, $postedAt, $userId): bool
    {
        $check = false;
        $fbChannelRow = FbChannel::where(['con_id' => $conversationId, 'hasExpired' => 0, 'user_id' => $userId])->first(['posted_at','ticket_id']);
        //replyInterval is the time after which the same conversation is new ticket, it is defined by user in Facebook Settings
        $replyInterval = FacebookApp::value('new_ticket_interval');
        if ($fbChannelRow) {
            if (strtotime($postedAt) < strtotime("+$replyInterval day", strtotime($fbChannelRow->posted_at))) {
                $check = true;
            } else {
                FbChannel::where('ticket_id', $fbChannelRow->ticket_id)->update(['hasExpired' => 1]);
            }
        }
        return $check;
    }

    /**
     * Checks whether user is in system
     * @param string $userName
     * @param string $email
     */
    private function checkUsers($userName, $email)
    {
        return User::where('user_name', $userName)->orWhere('email', $email)->first();
    }

    /**
     * If conversation already exists in the system; this methods adds message as reply
     * @param string $body
     * @param integer $userId
     * @param string $conversationID
     * @param array $attachments
     * @return mixed $result
     */
    private function reply($body, $userId, $conversationID, $attachments)
    {
        $ticketId = $this->getTicketIdForReply($conversationID);
        $request = AgentReplyRequest::create(
            url('/'),
            "POST",
            ['content' => "$body", 'do-not-send' => true],
            [],
            ['attachment' => $attachments]
        );
        return $this->ticketController->reply($request, $ticketId, false, false, $userId, false);
    }

    /**
     * Gets the ticket for replying
     * @param string $conversationId
     * @return integer $ticket_id
     */
    private function getTicketIdForReply($conversationId)
    {
        $channelTicket = FbChannel::where(['con_id'=>$conversationId,'hasExpired'=>0])->first(['ticket_id']);
        return ($channelTicket) ? $channelTicket->ticket_id : "";
    }

    /**
     * Inserts facebook messages into database
     * @param array $info
     * @param string $provider
     * @param string $via
     * @param string $ticketId
     * @return void
     */
    private function prepareForPersist($info, $provider, $via, $ticketId)
    {
        $info['via'] = $via;
        $info['provider'] = $provider;
        $info['ticket_id'] = $ticketId;
        FbChannel::create($info);
    }

    /**
     * Inserts facebook messages into database
     * @param array $info
     * @param array $result
     * @param string $provider
     * @param string $via
     * @return void
     */
    private function insertInfo($info, $result, $provider, $via)
    {
        $userId = $this->findUserFromTicketCreateUserId($result);
        $guestController = new GuestController(new PhpMailController());
        $user = $this->getUser($info);
        $guestController->update($userId, $user, $provider);
        $this->prepareForPersist($info, $provider, $via, $this->lastTicket($result));
    }

    /**
     * Finds the user who created the ticket
     * @param array $result
     * @return mixed
     */
    private function findUserFromTicketCreateUserId($result = [])
    {
        $ticket = $this->findTicketFromTicketCreateUser($result);
        return ($ticket) ? $ticket->user_id: null;
    }

    /**
     * Finds the ticket details based on th user who created it
     * @param array $result
     * @return mixed
     */
    private function findTicketFromTicketCreateUser($result = [])
    {
        //here result is obtained from TicketController Class; It is not associative array so have to check with index
        //it contains ticket_number
        if (isset($result[0])) {
            return Tickets::where('ticket_number', $result[0])->first(['id','user_id']);
        }
    }

    /**
     * Gets the last ticket
     * @param array $result
     * @return string $ticket_id
     */
    private function lastTicket($result)
    {
        $ticket = $this->findTicketFromTicketCreateUser($result);
        return ($ticket) ? $ticket->id: '';
    }

    /**
     * Extracts the User Information from Facebook related array
     * @param array $fields
     * @return array $user
     */
    private function getUser($fields)
    {
        $user = [];
        $user['username'] = isset($fields['username']) ? $fields['username']:'';
        $user['user_id'] = isset($fields['user_id']) ? $fields['user_id']:'';
        $user['avatar'] = isset($fields['avatar']) ? $fields['avatar']:'';
        $user['email'] = isset($fields['email']) ? $fields['email']:null;
        return $user;
    }
}
