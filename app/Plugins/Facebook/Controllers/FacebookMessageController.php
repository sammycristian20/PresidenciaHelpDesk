<?php


namespace App\Plugins\Facebook\Controllers;


use App\Http\Controllers\Controller;
use App\Plugins\Facebook\Model\FacebookMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FacebookMessageController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function message(Request $request)
    {
        $sender_id = $message_id = $page_id =  $text = null;
        $attachments = [];
        $data = $request->all();

        foreach ($data['entry'] as $entry) {
            foreach ($entry['messaging'] as $messaging) {
                $sender_id = $messaging['sender']['id'];
                $message_id = $messaging['message']['mid'];
                $text = (isset($messaging['message']['text'])) ? $messaging['message']['text'] : null;
                $page_id = $messaging['recipient']['id'];
                if (isset($messaging['message']['attachments'])) {
                    foreach ($messaging['message']['attachments'] as $attachment) {
                        $attachments[] = $attachment['payload']['url'];
                    }
                }
            }
        }

        FacebookMessage::create([
            'sender_id' => $sender_id,
            'message_id' => $message_id,
            'attachment_urls' => implode(",", $attachments),
            'posted_at' => Carbon::now(),
            'page_id' => $page_id,
            'message' => $text,

        ]);

        return response(null,200);
    }
}