<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class ClientTicketForm extends Event
{

    use SerializesModels;

    public $event;
    public $formType;
    public function __construct($event, $formType)
    {
        $this->event = $event;
        $this->formType = $formType;
    }
}
