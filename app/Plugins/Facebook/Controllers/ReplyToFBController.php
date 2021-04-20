<?php

namespace App\Plugins\Facebook\Controllers;

use App\Facades\Attach;
use App\Http\Controllers\Controller;
use App\Plugins\Facebook\Model\FacebookCredential;
use App\Plugins\Facebook\Model\FacebookMessage;
use DOMDocument;

class ReplyToFBController extends Controller
{
    private $filesToPurge = [];

    /**
     * Reply ticket on facebook
     * @param array $ticketReplyPayload
     * @return void
     * @throws \Exception
     */
    public function replyPageMessage($ticketReplyPayload)
    {
        $message = FacebookMessage::where('ticket_id', $ticketReplyPayload['ticket_id'])->first(['page_id','sender_id']);

        if ($message) {
            $pageId = $message->page_id;

            $senderId = $message->sender_id;

            $pageAccessInfo = FacebookCredential::where(['page_id' => $pageId, 'active' => 1])->first(['page_access_token']);

            if ($pageAccessInfo) {
                $batch = $this->prepareForReply($ticketReplyPayload, $senderId);
                $this->sendMessage($batch, $pageAccessInfo->page_access_token);
                $this->purgeFiles();
            } else {
                throw new \Exception(trans('Facebook::lang.failed_reply'));
            }
        }
    }

    /**
     * Deletes temporarily created files for sending to facebook
     */
    private function purgeFiles()
    {
        foreach ($this->filesToPurge as $file) {
            unlink(public_path($file));
        }
    }

    /**
     * This method handles sending message to facebook
     * @param $batch
     * @param $pageAccessToken
     * @throws \Exception
     */
    private function sendMessage($batch, $pageAccessToken)
    {
        foreach ($batch as $batchItem) {
            $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . $pageAccessToken);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($batchItem));
            $result = curl_exec($ch);

            $response = json_decode($result, true);

            if (isset($response['error'])) {
                throw new \Exception($response['error']['message']);
                break;
            }

            if (curl_errno($ch)) {
                throw new \Exception(curl_error($ch));
                break;
            }
            curl_close($ch);
        }
    }

    /**
     * Groups messages to send
     * @param $userId
     * @param null $attachment
     * @param null $inlineImage
     * @param null $body
     * @return array
     */
    private function getBatchRequestElements($userId, $attachment = null, $inlineImage = null, $body = null)
    {
        $message = [];
        if ($body) {
            $message = ["text" => $body];
        } else {
            $message = [
                "attachment" => [

                    "type" => ($inlineImage)
                        ? "image"
                        : $this->getAttachmentType($attachment['filename'], $attachment['disk']),

                    "payload" => [
                        "url" => ($inlineImage)
                            ? $this->rawEncodeFileName($inlineImage)
                            : $this->rawEncodeFileName(Attach::getUrlForPath($attachment['filename'], $attachment['disk']))
                    ]
                ]
            ];
        }
        return ["messaging_type" => 'UPDATE', "recipient" => ["id" => $userId], "message" => $message];
    }

    /**
     * Gets the type of attachment
     * @param $fileName
     * @param $disk
     * @return false|string
     */
    private function getAttachmentType($fileName, $disk)
    {
        $mimeType = Attach::getMimeTypeOfPath($fileName, $disk);
        $fileType = strstr($mimeType, '/', true);
        /*
         * FB requires every file other than image,audio,video to be of type `file`
         * https://developers.facebook.com/docs/messenger-platform/send-messages/#sending_attachments
         */
        return ($fileType !== 'application') ? $fileType : 'file';
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

    /**
     * Temporarily create files in public_path()
     * Note The file generated here will be deleted after sending that file url to facebook
     * @param $attachmentArray
     * @return string
     */
    private function constructAttachmentAndReturnUrl($attachmentArray)
    {
        $filename = $attachmentArray['filename'];

        $fileContents = file_get_contents($attachmentArray['pathname']);

        file_put_contents(public_path($filename), $fileContents);

        $this->filesToPurge[] = $filename;

        return $this->rawEncodeFileName(url($filename));
    }

    /*
     * Prepares body and attachment for replying
     * @param array $data
     * @return array
     */
    private function prepareForReply($data, $userId)
    {
        $batch = [];
        $attachments = (isset($data['attachment'])) ? $data['attachment'] : [];
        $inlineImages = $this->getInlineImagePaths($data['body']);
        $allowedExtensions = array_diff(config('filesystems.allowed_mime_types_public'), ['zip','rar','csv','txt']);
        foreach ($attachments as $attachment) {
            if (!in_array(strtolower($attachment['type']), $allowedExtensions)) {
                throw new \Exception(trans('Facebook::lang.facebook_invalid_format', [ 'formats' => implode(',', $allowedExtensions)]));
            }
            $batch[] = $this->getBatchRequestElements($userId, $attachment);
        }
        $allowedImages = ['jpg','jpeg','gif','png'];
        foreach ($inlineImages as $inlineImage) {
            if (!in_array(strtolower(pathinfo($inlineImage, PATHINFO_EXTENSION)), $allowedImages)) {
                continue;
            }
            $batch[] = $this->getBatchRequestElements($userId, null, $inlineImage);
        }
        $body = trim(strip_tags(str_replace(["&nbsp;","<p>","On","<td>","<li>"], [" "," <p>"," On"," <td>"," <li>"], $data['body'])));
        if ($body) {
            $batch[] = $this->getBatchRequestElements($userId, null, null, $body);
        }
        return $batch;
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
}
