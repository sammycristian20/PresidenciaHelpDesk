<?php

namespace App\Plugins\Chat\Controllers\LivServ;

use App\Http\Controllers\Controller;

class ProcessController extends Controller {

    private $request;
    private $helptopic;
    private $department;
    
    public function __construct($requests,$dept,$helptopic) {
        $this->request = $requests->all();
        $this->department = $dept;
        $this->helptopic = $helptopic;
    }
    /**
     * get the name
     * @return string
     */
    private function name() {
        $name = $this->checkArray('name', $this->request);
        return $name;
    }
    /**
     * get email
     * @return string
     */
    private function email() {
        $email = $this->checkArray('email', $this->request);
        return $email;
    }
    /**
     * get message
     * @return string
     */
    private function message() {
        $message = $this->checkArray('message', $this->request);
        return "<a href='$message' target='_blank'>Please click here to get details of chat</a>";
    }
    /**
     * get visitor phone number
     * @return string
     */
    private function phone() {
        $phone = $this->checkArray('phno', $this->request);
        return $phone;
    }
    /**
     * get visitor's phone code
     * @return string
     */
    private function phoneCode() {
        return "";
    }
    /**
     * get visitor's mobile number
     * @return string
     */
    private function mobile() {
        return "";
    }
    /**
     * get the application name
     * @return string
     */
    private function channel() {
        $app = $this->checkArray('app', $this->request);
        return $app;
    }
    /**
     * get ticket created through
     * @return string
     */
    private function via() {
        return "chat";
    }
    /**
     * subject of the ticket
     * @return string
     */
    private function subject() {
        $title = $this->checkArray('title', $this->request);
        return $title;
    }
    /**
     * get the value of an array using key
     * @param string $key
     * @param array $array
     * @return string
     */
    private function checkArray($key, $array) {
        $value = "";
        if (array_key_exists($key, $array)) {   
            $value = $array[$key];
        }
        return $value;
    }


    /**
     * Webhook Entry point
     */
    public function webhookEntry()
    {
        $this->createTicket();
    }


    /**
     * create ticket in system
     * @param object $controller
     */
    private function createTicket() {
        $ticketController = new \App\Http\Controllers\Agent\helpdesk\TicketController();
        $type = \App\Model\helpdesk\Manage\Tickettype::select('id')->first()->id ?: '';
        $priority = $ticketController->getSystemDefaultPriority();
        $source = $ticketController->getSourceByname('chat')->id;
        

        $result = $ticketController->create_user(
            $this->email(), $this->email(), 
            $this->subject(), $this->message(), $this->phone(), 
            $this->phoneCode(), $this->mobile(), 
            $this->helptopic, "", $priority, 
            $source, $headers = [], $this->department, 
            NULL, [], '','',$type
        );
        return json_encode($result);
    }

}
