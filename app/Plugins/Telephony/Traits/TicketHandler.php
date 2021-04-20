<?php
namespace App\Plugins\Telephony\Traits;

use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Manage\Help_topic as HelpTopic;

/**
 * Trait to provide helpers for ticket creation and updation.
 *
 * @author Manish Verma<manish.verma@ladybirdweb.com>
 * @since v3.0.0
 * @package App\Plugins\Telephony\Traits
 */
trait TicketHandler
{
    /**
     * @var array
     */
    protected $caller = null;

    /**
     * @var integer
     */
    protected $agent = null;

    /**
     * @var App\Plugins\Telephony\Model\TelephonyLog
     */
    protected $callLog;

    /**
     * Method which creates ticket for calls in the system
     *
     * @return Tickets $ticket
     */
	protected function ticketConversion():Tickets
	{
		$ticketController = new TicketController();
        $user = $this->caller;
        $body = $this->ticketBody();
        $department = Department::where('id', $this->callLog['intended_department_id'])->value('id')?: $ticketController->getSystemDefaultDepartment();
        $helptopic = HelpTopic::where('id', $this->callLog['intended_helptopic_id'])->value('id')?: $ticketController->getSystemDefaultHelpTopic();
        $source = $ticketController->getSourceByname('call')->id;
        $headers = [];
        $assignto = $this->agent;
        $fromData = [];
        $auto_response = "";
        $status = "";
        $subject = "New Call from ".(($user['name'])?: $this->callLog['call_from']);
        $sla = "";
        $priority = "";
        $type = "";
        $ticket_type = \App\Model\helpdesk\Manage\Tickettype::select('id')->first();
        if ($ticket_type) {
            $type = $ticket_type->id;
        }
        $result = $ticketController->create_user($user['email'], $user['username'], $subject, $body, $user['phone'], $user['country_code'], $user['mobile'], $helptopic, $sla, $priority, $source, $headers, $department, $assignto, $fromData, $auto_response, $status, $type, [], [], [], "", "", false, null);
        $ticket = $ticketController->findTicketFromTicketCreateUser($result);
        return $ticket;
    }

    /**
     * Method to get thread body for saving with call ticket
     * 
     * @return string $htmlString
     */
    private function ticketBody():string
    {
        $htmlString = trans('Telephony::lang.you_missed_call');
        $recored = $this->callLog['recording'];
        $created = $this->callLog['call_start_date'];
        if ($recored) {
            $htmlString = \Lang::get('Telephony::lang.listen-to-call-recording') . "<br><br><audio controls>
                        <source src=" . $recored . " type=\"audio/ogg\">
                        <source src=" . $recored . " type=\"audio/mpeg\">
                      Your browser does not support the audio element.
                      </audio><br><br>" . \Lang::get('Telephony::lang.incoming-call-recieved-on') . " " . faveodate($created);
        }

        return $htmlString;
    }

    /**
     * Method adds saved notes on call logs to tickets as internal notes
     *
     * @param  int   $ticketId  ticket id on which note should be added
     * @return void
     */
    private function addCallNotes(int $ticketId):void
    {
        if($this->callLog['notes']) {
            (new TicketController)->saveReply($ticketId, $this->callLog['notes'], $this->agent, false, [], [], true, 'agent', [],'',[], false, false, true);
        }
    }

    /**
     * Method links the call recording to the given ticket
     *
     * @param   int      $ticketId  ticket id on which call should be linked
     * @return  Tickets
     */
    private function linkCallToTicket(int $ticketId):Tickets
    {
        return (new TicketController)->saveReply($ticketId, $this->ticketBody(), $this->caller['id'], false, [], [], true, 'client', [],'',[], false, false, false)->ticket;
    }
}
