<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use App\Location\Models\Location;

class RecurTicket implements ShouldQueue
{

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;
    public $tries = 5;
    public $values;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tickets)
    {
        $this->values = $tickets;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userId       = $this->values['requester'];
        $subject       = $this->values['subject'];
        $body          = $this->values['body'];
        $helptopic     = $this->values['help_topic'];
        $sla           = $this->values['sla'];
        $priority      = $this->values['priority'];
        $source        = $this->values['source'];
        $headers       = $this->values['cc'];
        $dept          = $this->values['dept'];
        $assignto      = $this->values['assigned'];
        $from_data     = $this->values['extra_form'];
        $status        = $this->values['status'];
        $type          = $this->values['type'];
        $attachment    = $this->values['attachments'];
        $inline        = $this->values['inline'];
        $email_content = $this->values['email_content'];
        $company       = $this->values['company'];
        $locationId    = $this->values['location'];
        $user = $this->getUser($userId);
        $parentTicketId = (!empty($this->values['parent_ticket_id'])) ? $this->values['parent_ticket_id'] : null;
        $this->ticketController()->create_user($user->email, $user->user_name, $subject, $body, "", "", "", $helptopic, $sla, $priority, $source, $headers, $dept, $assignto, $from_data, "", $status, $type, $attachment, $inline, $email_content, $company, 0,true,$locationId, $parentTicketId);
    }

    public function getUser($userId){
        return \App\User::whereId($userId)->select('id','user_name','email','phone_number','ext','mobile')->first();
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
       \Logger::exception($exception , 'ticket-create');
    }

    public function ticketController()
    {
        return new \App\Http\Controllers\Agent\helpdesk\TicketController();
    }
}
