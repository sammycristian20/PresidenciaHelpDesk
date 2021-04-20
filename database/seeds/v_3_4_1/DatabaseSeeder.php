<?php

namespace database\seeds\v_3_4_1;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Notification\Notification;
use App\Model\helpdesk\Ticket\Tickets;

/** 
 * Database seeder class for v3.4.1
 * @package database\seeds\v_3_4_1
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 */
class DatabaseSeeder extends Seeder
{
    /**
     * method to execute database seeds
     * @return void
     */
    public function run()
    {
        // Update response due approach
        $this->updateNotificationForTickets('response_due_approach', ":ticketId has response due on :duedate");
        // Update resolution due approach
        $this->updateNotificationForTickets('resolve_due_approach', ":ticketId has resolution due on :duedate");
        // Update response due violate
        $this->updateNotificationForTickets('response_due_violate', ":ticketId had response due on :duedate");
        // Update resolution due violate
        $this->updateNotificationForTickets('resolve_due_violate', ":ticketId had resolution due on :duedate");
    }

    /**
     * Returns an array containing unique row_id=> ticket id in notification
     * which has message like $storedMessage
     * @param   string $storedMessage  string to search in notifications message column
     * @return  array                  array containing unique ticket ids
     */
    private function getDistinctTicketIdFromNotifications(string $storedMessage):array
    {
        return Notification::where('message', 'LIKE', "%$storedMessage%")->distinct('row_id')->pluck('row_id')->toArray();
    }

    /**
     * Fucntion updates incorrectly stored notifications for Response/Resolution
     * SLA approach/violation by replacing :ticketId and :duedate to their values
     * 
     * @param   string  $scenario       tells which notification we are updating
     * @param   string  $storedMessage  value stored in database as notification
     *                                  for that scenario
     * @return  void
     */
    private function updateNotificationForTickets(string $scenario, string $storedMessage):void
    {
        $ticketIds = $this->getDistinctTicketIdFromNotifications($storedMessage);
        foreach ($ticketIds as $ticketId) {
            $ticket = Tickets::find($ticketId);
            if(!$ticket) continue;
            $message = trans("lang.{$scenario}_notification", [
                "ticketId"=>$ticket->ticket_number,
                "duedate"=>($ticket->duedate) ? faveodate($ticket->duedate) : "NA (duedate updated or set to null)"
            ]);
            $url = url("/thread/$ticket->id");
            Notification::where([
                ['row_id', '=', $ticketId],
                ['message', 'LIKE', "%$storedMessage%"]
            ])->update([
                'message' => $message,
                'url'     => $url
            ]);
        }
    }
}
