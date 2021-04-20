<?php

namespace App\Plugins\Facebook\Cron;

use App\Facades\Attach;
use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Http\Requests\helpdesk\AgentReplyRequest;
use App\Model\helpdesk\Manage\Tickettype;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\Facebook\FB\Exceptions\FacebookSDKException;
use App\Plugins\Facebook\FB\Facebook;
use App\Plugins\Facebook\Handlers\FBLaravelPersistantDataHandler;
use App\Plugins\Facebook\Mimes\Mime;
use App\Plugins\Facebook\Model\FacebookApp;
use App\Plugins\Facebook\Model\FacebookCredential;
use App\Plugins\Facebook\Model\FacebookMessage;
use App\Plugins\Facebook\Model\FacebookPages;
use App\Plugins\Facebook\Model\FbChannel;
use DateTime;
use DateTimeZone;
use Illuminate\Http\UploadedFile;

class FacebookCronProcessor
{
    private $ticketController;

    public function __construct()
    {
        //used by most methods of this class, so constructing
        $this->ticketController = new TicketController();
    }

    public function processFacebookMessages()
    {
        $result = $ticket = null;
        $messages = FacebookMessage::where('processed', 0)->get()->toArray();
        foreach ($messages as $message) {
            if (!$this->isPageActive($message['page_id'])) {
                continue;
            }

            list('username' => $username,'email' => $email,'userId' => $userId,'body' => $body,'attachments' => $attachments, 'messageId' => $messageId, 'pageId' => $pageId) = $this->prepareTicketData($message);

            $replyDetails = $this->isThisMessageReply($message);

            if ($replyDetails) {
                $result = $this->reply($body, $replyDetails['ticket_id'], $attachments);
            } else {
                $result = $this->ticketController->create_user(
                    $email,
                    $username,
                    'Facebook Message from ' . $username,
                    $body,
                    '',
                    '',
                    '',
                    Ticket::value('help_topic') ?: '',
                    '',
                    '',
                    $this->ticketController->getSourceByname("facebook")->id,
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
                $ticket = Tickets::where('ticket_number', $result[0])->value('id');
            } else {
                $ticket = $result->ticket_id;
            }

            if ($ticket) {
                FacebookMessage::where(
                    ['sender_id' => $userId, 'page_id' => $message['page_id'], 'message_id' => $message['message_id']]
                )->update(['ticket_id' => $ticket, 'processed' => 1]);
            }
        }
    }

    /**
     * Checks the page is inactive
     * @param $pageId
     * @return bool
     */
    private function isPageActive($pageId)
    {
        return (bool) FacebookCredential::where(['page_id' => $pageId, 'active' => 1])->count();
    }

    /**
     * If the incoming message is a reply to already created ticket
     * this will return the ticket ID of the parent ticket
     * @param $message array of message fields
     * @return array
     */
    private function isThisMessageReply($message)
    {
        $replyDetails = [];
        $reply = FacebookMessage::where([
            ['sender_id','=',$message['sender_id']],
            ['page_id','=',$message['page_id']],
            ['processed','=',1],
            ['ticket_id','!=',null]
        ])->orderBy('ticket_id', 'desc')->first(['posted_at','ticket_id']);

        if ($reply) {
            $interval = FacebookCredential::where('page_id', $message['page_id'])->value('new_ticket_interval');

            if (strtotime($message['posted_at']) < strtotime("+$interval day", strtotime($reply->posted_at))) {
                $replyDetails['ticket_id'] = $reply->ticket_id;
            }
        }
        return $replyDetails;
    }

    private function prepareAttachments($message)
    {
        $attachments = $uploads = [];
        if ($message['attachment_urls']) {
            $attachments = explode(',', $message['attachment_urls']);

            foreach ($attachments as $attachment) {
                $contents = file_get_contents($attachment);

                $fileInfo = $this->getFileInformation($attachment);

                //save image
                $filename = $fileInfo['filename'] . '.' . $fileInfo['extension'];
                $fileLocation = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
                file_put_contents($fileLocation, $contents);

                $datewiseFolder = now()->year . '/' . now()->month . '/' . now()->day;

                $newPath = Attach::put("multimedia_private/$datewiseFolder", new UploadedFile($fileLocation, basename($fileLocation), null, 0, false), FileSystemSettings::value('disk'), null, false);

                $uploads['uploads'][] = $this->makeAttachmentsFromPath($newPath);
            }
        }
        return $uploads;
    }


    /**
     * Appends Attachments for tickets
     * @param $imagePath
     * @return array
     */
    private function makeAttachmentsFromPath($imagePath)
    {
        $disk = FileSystemSettings::value('disk');

        $metaData = Attach::getMetadata($imagePath, $disk);

        return [
            'filename' => $imagePath,
            'size' => $metaData['size'],
            'type' => pathinfo($imagePath, PATHINFO_EXTENSION),
            'path' => $imagePath,
            'disk' => $disk
        ];
    }

    private function getFileInformation($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path_parts = pathinfo($path);
        return ['filename' => $path_parts['filename'], 'extension' => $path_parts['extension']];
    }

    private function getUsernameFromFacebookUsingPSID($userId, $pageId)
    {
        $pageAccessToken = FacebookCredential::where('page_id', $pageId)->value('page_access_token');

        $ch = curl_init();
        $url = "https://graph.facebook.com/$userId?fields=name&access_token=$pageAccessToken";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        $userDetails = json_decode($result, true);
        curl_close($ch);

        return (isset($userDetails['name'])) ? $userDetails['name'] : $userId;
    }

    private function prepareTicketData($message)
    {
        $ticketColumns = [];
        $ticketColumns['username'] = $this->getUsernameFromFacebookUsingPSID($message['sender_id'], $message['page_id']);
        $ticketColumns['email'] = $message['sender_id'] . "@facebook.com";
        $ticketColumns['userId'] = $message['sender_id'];
        $ticketColumns['body'] = ($message['message']) ?: "\u{200C}";
        $attachmentsArray = $this->prepareAttachments($message);
        $ticketColumns['attachments'] = [];
        if ($attachmentsArray) {
            $ticketColumns['attachments'] = $attachmentsArray['uploads'];
        }
        $ticketColumns['pageId'] = $message['page_id'];
        $ticketColumns['messageId'] = $message['message_id'];

        return $ticketColumns;
    }

    /**
     * If conversation already exists in the system; this methods adds message as reply
     * @param string $body
     * @param $ticketId
     * @param array $attachments
     * @return mixed $result
     */
    private function reply($body, $ticketId, $attachments)
    {
        $request = AgentReplyRequest::create(
            url('/'),
            "POST",
            ['content' => "$body", 'do-not-send' => true],
            [],
            ['attachment' => $attachments]
        );
        return $this->ticketController->saveReply(
            $ticketId,
            $body,
            requester($ticketId),
            true,
            $attachments,
            [],
            true,
            "client"
        );
    }

    /**
     * Gets the ticket for replying
     * @param $pageId
     * @param $senderId
     * @return integer $ticket_id
     */
    private function getTicketIdForReply($pageId, $senderId)
    {
        $replyRow = FacebookMessage::where(['sender_id' => $senderId, 'page_id' => $pageId, 'processed' => 1])->first(['ticket_id']);
        return ($replyRow) ? $replyRow->ticket_id : "";
    }
}
