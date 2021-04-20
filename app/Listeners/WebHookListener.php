<?php

namespace App\Listeners;

use App\User;
use App\Events\WebHookEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class WebHookListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    protected $url;
    public function __construct()
    {
        $this->url = apiSettings('web_hook');
    }
     /**
     * Handle the event.
     *
     * @param  WebHookEvent  $event
     * @return void
     */
    public function handle(WebHookEvent $webhook)
    {   
        try{
            $url = apiSettings('web_hook');
            if ($url) {
                if(\Auth::user()->role != 'user'){
                    switch($webhook->event){
                        case "ticket_status_updated": 
                            $parameters = [
                                "event" => "ticket_status_updated",
                                "ticket" => $webhook->ticket->toArray(),
                            ];
                            \Log::info("\n\n\nticket_status_updated webhook executed\n\n\n");
                            \Log::info($parameters);
                            \Log::info("url: ". $this->url);
                            $this->allWebHook($this->url, $parameters);
                        break;

                        case "ticket_created":
                            \Log::info("\n\n\n ticket_created webhook executed\n\n\n");
                            \Log::info($webhook->all_params);
                            \Log::info("url: ". $this->url);
                            $this->allWebHook($this->url, $webhook->all_params);
                        break;

                        case "ticket_commented":
                          
                            \Log::info("\n\n\nticket_commented webhook executed\n\n\n");
                            \Log::info($parameters);
                            \Log::info("url: ". $this->url);
                            $this->allWebHook($this->url, $parameters);
                        break;

                        case "ticket_reply":
                            $parameters = [
                                "event" => "ticket_reply",
                                "replied_by" => User::where('id', $webhook->all_params->user_id)->select('id', 'first_name', 'last_name', 'email', 'user_name')->first()->toArray(),
                                "thread" => $webhook->all_params->toArray(),
                            ];
                            \Log::info("\n\n\n ticket_reply webhook executed\n\n\n");
                            \Log::info($parameters);
                            \Log::info("url: ". $this->url);
                            $this->allWebHook($this->url, $parameters);
                        break;

                        case "ticket_assigned":
                            $parameters = [
                                "event" => "ticket_assigned",
                                "ticket" => $webhook->all_params->toArray(),
                            ];
                            \Log::info("\n\n\n ticket_department_updated webhook executed\n\n\n");
                            \Log::info($parameters);
                            \Log::info("url: ". $this->url);
                            $this->allWebHook($this->url, $parameters);
                        break;

                        case "ticket_department_updated":
                            $parameters = [
                                "event" => "ticket_department_updated",
                                "thread" => $webhook->ticket->toArray(),
                            ];
                            \Log::info("\n\n\n ticket_department_updated webhook executed\n\n\n");
                            \Log::info($parameters);
                            \Log::info("url: ". $this->url);
                            $this->allWebHook($this->url, $parameters);
                        break;

                        case "ticket_owner_updated":
                        \Log::info($webhook->ticket);
                            \Log::info("\n\n\nticket_owner_updated webhook executed\n\n\n");
                            \Log::info($webhook->ticket);
                            \Log::info("url: ". $this->url);
                            $this->allWebHook($this->url, $webhook->ticket);
                        break;

                        case "ticket_due_date_updated":
                            $parameters = [
                                "event" => "ticket_due_date_updated",
                                "thread" => $webhook->ticket->toArray(),
                            ];
                            \Log::info("\n\n\n ticket_due_date_updated webhook executed\n\n\n");
                            \Log::info($parameters);
                            \Log::info("url: ". $this->url);
                            $this->allWebHook($this->url, $parameters);
                        break;
                    }
                }
            }
        }
        catch(\Exception $e){
            \Log::info("Webhook Exception caught:   ".$e->getMessage());
        }
    }

    public function allWebHook($url, $parameters = array(), $method = 'POST')
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request($method, $url, [
            'form_params' => $parameters
        ]);
        \Log::info($response->getStatusCode());
        $result   = $response->getBody()->getContents();
        if (is_array($result)) {
            $result = json_encode($result);
        }
    }
}
