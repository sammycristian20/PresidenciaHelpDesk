<?php

namespace App\Plugins\Whatsapp\Controllers;

use App\Facades\Attach;
use App\Http\Controllers\Controller;
use App\Model\helpdesk\Ticket\Ticket_source;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\Whatsapp\Model\Whatsapp;
use App\Plugins\Whatsapp\Model\WhatsAppMessage;
use Carbon\Carbon;
use DOMDocument;
use Twilio\Rest\Client;
use Logger;

class WhatsappReplyController extends Controller
{
    /*
     * Check if the 24 hr session has timed out
     * @param $ticketId
     * @return string
     */
    private function checkSessionHasTimedOut($ticketId)
    {
        $lastMessageInboundDate = WhatsAppMessage::where('ticket_id', $ticketId)->latest('id')->first(['posted_at']);
        $lastMessageInboundDateCarbon = Carbon::parse($lastMessageInboundDate->posted_at);
        $today = Carbon::now()->toDateTimeString();
        return $lastMessageInboundDateCarbon->diffInDays($today) > 1;
    }

    /**
     * Checks whether the ticket's source is whatsapp or not.
     * @param $ticketId
     * @return bool
     */
    private function isTicketSourceWhatsApp($ticketId)
    {
        $isSourceWhatsApp = false;
        $ticketSource = Tickets::where('id', $ticketId)->value('source');
        if ($ticketSource) {
            $isSourceWhatsApp = Ticket_source::where('id', $ticketSource)->value('name') == "Whatsapp";
        }
        return $isSourceWhatsApp;
    }

    /**
     * Post the agent reply to whatsapp.
     * @param array $data
     * @throws \Exception
     */
    public function sendReply($data)
    {
        $ticketId = $data['ticket_id'];
        $isSourceWhatsApp = $this->isTicketSourceWhatsApp($ticketId);
        $whatsAppMessage = (WhatsAppMessage::where('ticket_id', $ticketId)->first(['from']));

        if ($whatsAppMessage && $isSourceWhatsApp) {
            $WhatsAppApplication = WhatsApp::first(['template','sid','token','business_phone']);
            $whatsapp = new Client($WhatsAppApplication->sid, $WhatsAppApplication->token);
            $businessPhone = $WhatsAppApplication->business_phone;
            $payload = ["from" => "whatsapp:" . $businessPhone];

            if ($this->checkSessionHasTimedOut($ticketId)) {
                $template = $WhatsAppApplication->template;
                if (!$template) {
                    throw new \Exception(trans('Whatsapp::lang.whatsapp_no_template'));
                } else {
                    preg_match("/{{(.*?)}}/", $template, $matches);
                    $template = str_replace($matches[0], strip_tags($data['body']), $template);
                    $payload['body'] = trim($template);
                }
            } else {
                $replyData = $this->prepareForReply($data);
                $payload['body'] = trim($replyData['body']);
                if (!empty($replyData['batch'])) {
                    if (count($replyData['batch']) > 1) {
                        throw new \Exception(trans('Whatsapp::lang.whatsapp_only_one_media'));
                    } else {
                        $payload['mediaUrl'] = [array_pop($replyData['batch'])];
                    }
                }
            }

            $whatsapp->messages->create($whatsAppMessage->from, $payload);
        }
    }

    /*
     * Returns the array consisting of paths of inline images in body of reply message
     * @param string $body
     * @return array
     */
    private function getInlineImagePaths($body)
    {
        $inlineImages = [];
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML(mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();
        $tags = $doc->getElementsByTagName('img');
        foreach ($tags as $tag) {
            $inlineImages[] = $tag->getAttribute('src');
        }
        return $inlineImages;
    }

    /*
     * Prepares body and attachment for replying
     * @param array $data
     * @return array
     */
    private function prepareForReply($data): array
    {
        $batch = [];
        $allowedExtensions = ['JPG', 'JPEG', 'PNG','MP3', 'OGG', 'AMR','PDF','MP4'];
        $attachments = (isset($data['attachment'])) ? $data['attachment'] : [];
        $inlineImages = $this->getInlineImagePaths($data['body']);
        foreach ($attachments as $attachment) {
            if (!in_array(strtoupper($attachment['type']), $allowedExtensions)) {
                throw new \Exception(trans('Whatsapp::lang.whatsapp_invalid_format', [ 'formats' => implode(',', $allowedExtensions)]));
            }
            $batch[] = $this->rawEncodeFileName(Attach::getUrlForPath($attachment['filename'], $attachment['disk']));
        }
        $allowedImages = ['JPG', 'JPEG', 'PNG'];
        foreach ($inlineImages as $inlineImage) {
            if (!in_array(strtoupper(pathinfo($inlineImage, PATHINFO_EXTENSION)), $allowedImages)) {
                continue;
            }
            $batch[] = $this->rawEncodeFileName($inlineImage);
        }
        $body = trim(strip_tags(str_replace(["&nbsp;","<p>","On","<td>","<li>"], [" "," <p>"," On"," <td>"," <li>"], $data['body'])));
        return compact('batch', 'body');
    }

    /*
     * Url Encodes the filename properly and returns
     * @param $url
     * @return $encodedUrl
     */
    private function rawEncodeFileName($url)
    {
        $parts = parse_url($url);
        $fileName = basename($parts['path']);
        $newName = rawurlencode(basename($fileName));
        return str_replace($fileName, $newName, $url);
    }
}
