<?php

namespace App\Plugins\Whatsapp\Controllers;

use File;
use Logger;
use App\User;
use DateTime;
use DateTimeZone;
use App\Facades\Attach;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use Twilio\Security\RequestValidator;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Settings\Ticket;
use App\Plugins\Whatsapp\Model\WhatsApp;
use App\Model\helpdesk\Manage\Tickettype;
use App\Plugins\Whatsapp\Model\WhatsAppMessage;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Plugins\Whatsapp\Model\WhatsappWebhookStore;
use App\Plugins\Whatsapp\Services\CurlFileRetriever;
use App\Plugins\Whatsapp\MimeTypes\MimeTypeConverter;
use App\Http\Controllers\Agent\helpdesk\TicketController;

class WhatsappController extends Controller
{
    /**
     * @var Object Whatsapp object
     */
    private $whatsApp;

    /**
     * @var Array Allowed MIME Types
     */
    private $allowed = [
        "image/png","image/jpg",
        "image/jpeg","video/mp4",
        "audio/mpeg","audio/ogg",
        "application/pdf","audio/mpeg3",
        "audio/x-mpeg-3"
    ];


    public function __construct()
    {
        $this->whatsApp = WhatsApp::first(['is_image_inline','token']);
    }

    /**
     * This method is for Twilio "When a message comes in" Webhook.
     * The request will be sent by twilio.
     * We are simply storing the twilio request payload in WhatsappWebhookStore Model For Further Processing.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function webhook(Request $request)
    {
        if (!$this->isRequestToThisWebhookSecure($request)) {
           /*
            * this webhook entry point,It gets called whenever user creates a new message comes
            * if I throw exception here there will be no use
            * so logging it here so that agent can inspect the situation
            */
            Logger::exception(new \Exception(trans('Whatsapp::lang.whatsapp_webhook_error')));
        } else {
            WhatsappWebhookStore::create($request->all());
        }

        //Twilio requires response to be "204 No Content" if we are not sending one message back right away,
        return response()->noContent();
    }

    /**
     * This Method Processes the Twilio Request Payload Stored in WhatsappWebhookStore
     * and Generate ticket/thread
     * @param void
     * @return void
     */
    public function webhookProcess()
    {
        //all columns are needed when processing webhook entries.
        $WhatsAppMessageStore = WhatsappWebhookStore::get()->toArray();
        foreach ($WhatsAppMessageStore as $message) {
            if ($message['NumMedia'] == 1) {
                $mediaUrl = $message['MediaUrl0'];
                $mimeType = $message['MediaContentType0'];

                if (!in_array($mimeType, $this->allowed)) {
                    continue;
                }

                $fileContent = (new CurlFileRetriever())->requestContent($mediaUrl);

                $fileExtension = (new MimeTypeConverter())->toExtension($mimeType);

                $pathParts = explode('/', parse_url($mediaUrl)['path']);

                $mediaSid = end($pathParts);

                $storagePath = $this->saveFile("{$mediaSid}.{$fileExtension}", $fileContent);
                
                $datewiseFolder = now()->year . '/' . now()->month . '/' . now()->day;

                $newPath = Attach::put("multimedia_private/$datewiseFolder", new UploadedFile($storagePath, basename($storagePath), null, 0, false), FileSystemSettings::value('disk'), null, false);

            
                $this->generateTicket($message['Body'], $message['From'], $newPath, $mimeType);
            } else {
                $this->generateTicket($message['Body'], $message['From']);
            }
        }
        //clearing payload
        WhatsappWebhookStore::query()->truncate();
    }

    /**
     * Saves the file content to filename
     * @param string $filename
     * @param string $content
     * @return string
     */
    private function saveFile(string $filename, string $content)
    {
        $fileLocation = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($fileLocation, $content);
        return $fileLocation;
    }

    /**
     * Determine attachment type is inline or no.
     * Current Implementation (image => inline unless not set in settings explicitly) (rest => attachment)
     * @param string $mimeType
     * @return boolean
     */
    private function isThisAttachmentNotInline($mimeType)
    {
        $imageArray = ["image/png","image/jpg","image/jpeg"];
        if (!in_array($mimeType, $imageArray)) {
            //attachment is not image, so there is no question of whether inline or not, always not inline
            return true;
        } else {
            //these are images, hence have to determine whether inline or not.
            return (bool) !$this->whatsApp->is_image_inline;
        }
    }

    /**
     * Generates the ticket
     * @param string $body
     * @param string $from
     * @param string $storagePath
     * @param string $mimeType

     */
    private function generateTicket($body, $from, $storagePath = "", $mimeType = "")
    {
        $ticketController = new TicketController();
        $fromWithoutProtocol = str_replace("whatsapp:", '', $from); //generally twilio returns number in whatsapp:+9112333675 with whatsapp protocol identifier so stripping it.
        $phoneDetails = resolve('libphonenumber')->parse($fromWithoutProtocol, null);
        $user = $this->checkUser($phoneDetails->getNationalNumber());
        if ($user) {
            $userName = $user->user_name;
            $userId = $user->id;
        } else {
            $userName = str_replace("+", '', $fromWithoutProtocol);
            $userId = $fromWithoutProtocol;
        }
        $inline = $attach = $producedAttachments = [];
        $source = $ticketController->getSourceByname("Whatsapp")->id;
        if ($storagePath) {
            $producedAttachments = $this->makeAttachmentsFromPath($storagePath);
            $isAttachmentNotInline = $this->isThisAttachmentNotInline($mimeType);
            ($isAttachmentNotInline)
                ? array_push($attach, [$producedAttachments])
                : array_push($inline, [$producedAttachments]);
        }
        $body = "<p>$body</p>";
        if ($producedAttachments && !$isAttachmentNotInline) {
            $body .= "<figure class='image'><img src='" . basename($storagePath) . "'></figure>";
        }
        if ($replyDetails = $this->isThisMessageAReply($from)) {
            if ($user->active == '1') {
                $resultOfReply = $ticketController->saveReply(
                    $replyDetails['ticket_id'],
                    $body,
                    $replyDetails['user_id'],
                    "",
                    $attach,
                    $inline,
                    false,
                    'client'
                );
                $this->persistMessageInfoAboutReply($from, $resultOfReply);
            }
        } elseif ($user) {
            if ($user->active == '1') {
                $result = $ticketController->createTicket(
                    $userId,
                    "Message from Whatsapp",
                    $body,
                    Ticket::value('help_topic') ?: '',
                    '',
                    '',
                    $source,
                    [],
                    System::value('department'),
                    null,
                    [],
                    "",
                    Tickettype::value('id') ?: '',
                    $attach,
                    $inline,
                    $email_content = [],
                    $company = "",
                    $domainId = ""
                );
                $this->persistMessageInfoAboutNewIncoming($from, $result);
            }
        } else {
            $result = $ticketController->create_user(
                $userName,
                $userId,
                "Message from Whatsapp",
                $body,
                "",
                $phoneDetails->getCountryCode(),
                $phoneDetails->getNationalNumber(),
                Ticket::value('help_topic') ?: '',
                '',
                '',
                $source,
                [],
                System::value('department'),
                null,
                [],
                "",
                "",
                Tickettype::value('id') ?: '',
                $attach,
                $inline
            );

            if (is_array($result)) {
                $this->persistMessageInfoAboutNewIncoming($from, $result);
            }
        }
    }


    /**
     * Checks whether the user exists in the system based on the mobile number
     * From which the whatsapp message is recieved.
     * @param string $from
     * @return mixed
     */
    private function checkUser($from)
    {
        $user = User::where('mobile', $from)->first(['id','user_name', 'active']);
        return ($user) ?: null;
    }

    /**
     * Checks whether this message is a Reply to any previous ticket
     * @param string $from
     * @return array
     */
    private function isThisMessageAReply($from)
    {
        $ticketController = new TicketController();
        $checkReplyTime = true;
        $isTimeConstraint = false;
        $phone = resolve('libphonenumber');
        $phoneDetails = $phone->parse(str_replace('whatsapp:', '', $from), null);
        $user = $this->checkUser($phoneDetails->getNationalNumber());
        $userOfTicketId = ($user) ? $user->id : '';
        $daysAfterReplyIsNewTicket = WhatsApp::first()->new_ticket_interval;
        $whatsAppMessageStore = WhatsAppMessage::where('from', $from)->latest('id')->first(['posted_at','ticket_id']);
        $today = $this->formatTimezone((date('Y-m-d H:i:s')));

        if ($whatsAppMessageStore) {
            $checkReplyTime = (! (bool) Tickets::where('id', $whatsAppMessageStore->ticket_id)->count())
                ? true : $ticketController->checkReplyTime($whatsAppMessageStore->ticket_id);

            $lastReplyDateWithInterval = $this->formatTimezone((date('Y-m-d H:i:s', strtotime($whatsAppMessageStore->posted_at . " +$daysAfterReplyIsNewTicket Days"))));

            $isTimeConstraint = ($today <= $lastReplyDateWithInterval);
        }

        return ($whatsAppMessageStore && $isTimeConstraint && !$checkReplyTime)
            ? ["ticket_id" => $whatsAppMessageStore->ticket_id,"user_id" => $userOfTicketId] : [];
    }

    /**
     * Saves information about whatsapp message which is a reply.
     * @param string $from
     * @param array $result_of_reply
     */
    private function persistMessageInfoAboutReply($from, $result_of_reply)
    {
        $whatsAppMessages = array(
            'from' => $from,
            'ticket_id' => $result_of_reply['ticket_id'],
            'posted_at' => $this->formatTimezone(date('Y-m-d H:i:s'))
        );

        WhatsAppMessage::create($whatsAppMessages);
        $this->updateTimestampsOfLastReplyFromUserToStartMessagingSession($from);
    }

    /**
     * Formats the message created time to UTC
     * @return string
     */
    private function formatTimezone($dateTime)
    {
        $dt = new DateTime($dateTime, new DateTimeZone('UTC'));
        return $dt->format('Y-m-d H:i:s');
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

    /**
     * Saves information about whatsapp message which is a new incoming.
     * @param string $from
     * @param array $result
     */
    private function persistMessageInfoAboutNewIncoming($from, $result)
    {
        $tickets = new Tickets();
        //here result is obtained from TicketController Class; It is not associative array so have to check with index
        //it contains ticket_number
        $ticket = (is_array($result))
            ? $tickets->where('ticket_number', $result[0])->first(['id'])
            : $tickets->where('ticket_number', $result)->first(['id']);
        $whatsAppMessage = array(
            'from' => $from,
            'ticket_id' => $ticket->id,
            'posted_at' => $this->formatTimezone(date('Y-m-d H:i:s'))
        );

        WhatsAppMessage::create($whatsAppMessage);
        $this->updateTimestampsOfLastReplyFromUserToStartMessagingSession($from);
    }

    /**
     * Confirms Request to webhook is coming from Twilio or not,
     * @param \Illuminate\Http\Request $request
     * @return boolean
     */

    private function isRequestToThisWebhookSecure(Request $request)
    {
        $requestValidator = new RequestValidator($this->whatsApp->token);
        $requestData = $request->toArray();

        if (array_key_exists('bodySHA256', $requestData)) {
            $requestData = $request->getContent();
        }

        return $requestValidator->validate(
            $request->header('X-Twilio-Signature'),
            $request->fullUrl(),
            $requestData
        );
    }

    /**
     * updates the timestamp in WhatsAppMessage to start 24 Messaging Session Window
     * @param $from
     */
    private function updateTimestampsOfLastReplyFromUserToStartMessagingSession($from)
    {
        WhatsAppMessage::where('from', $from)->update(['posted_at' => $this->formatTimezone(date('Y-m-d H:i:s'))]);
    }
}
