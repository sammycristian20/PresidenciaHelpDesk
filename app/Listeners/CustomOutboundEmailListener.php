<?php

namespace App\Listeners;

use App\Events\CustomOutboundEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomOutboundEmailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    /**
     * Handle the event.
     *
     * @param  CustomOutboundEmail  $event
     * @return void
     */
    public function handle(CustomOutboundEmail $event)
    {
        $to_name      = $event->to_name;
        $to_email     = $event->to_email;
        $subject      = $event->subject;
        $data         = $event->data;
        $cc           = $event->cc;
        $attach       = $event->attach;
        $thread       = $event->thread;
        $auto_respond = $event->auto_respond;
        $this->mailRelay($to_name, $to_email, $subject, $data, $cc, $attach, $thread, $auto_respond);
    }
    public function mailRelay($to_name, $to_email, $subject, $data, $cc, $attach, $thread, $auto_respond)
    {
        $apiKey   = config('services.mailrelay.apikey');
        $url      = config('services.mailrelay.url');
        $content  = view('emails.mail', ['data' => $data, 'thread' => $thread])->render();
        $to       = [[
        'name'  => $to_name,
        'email' => $to_email
        ]];
        $postData = array(
            'function'        => 'sendMail',
            'apiKey'          => $apiKey,
            'subject'         => $subject,
            'html'            => $content,
            'mailboxFromId'   => 1,
            'mailboxReplyId'  => 1,
            'mailboxReportId' => 1,
            'packageId'       => 6,
            'emails'          => $to,
            'attachments'     => [],
        );
        $this->guzzle($url, $postData);
    }
    public function guzzle($url, $parameters, $method = 'POST')
    {
        $client = new \GuzzleHttp\Client();
        $res    = $client->request($method, $url, ['form_params'=>$parameters]);
        $result = $res->getBody()->getContents();
        loging('mailrelay', $result, 'info');
        if ($res->getBody()) {
            
        }
    }
}
