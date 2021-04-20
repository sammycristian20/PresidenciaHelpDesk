<?php

namespace database\seeds\v_2_3_0;

use App\Model\helpdesk\Ticket\Ticket_Thread as Thread;
use database\seeds\DatabaseSeeder as Seeder;
use DB;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\Common\TicketActivityLog;
use Config;

class DatabaseSeeder extends Seeder
{
    /**
     * method to execute database seeds
     * @return void
     */
    public function run()
    {
        $this->removeResolvedStatus();
        $this->avgResponseTimeSeeder();
    }

    private function avgResponseTimeSeeder()
    {
        // it is gonna take all the tickets in the memory, so making memory as maximum available
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');
        set_time_limit(0);

        Thread::select('ticket_id', DB::raw('AVG((response_time)) as avg_response_time'))
            ->groupBy('ticket_id')->get()->map(function ($thread) {
                // updating ticket table without using model, so that no eloquent event gets fired
                DB::table('tickets')->where('id', $thread->ticket_id)
                    ->update(['average_response_time'=> 'avg_response_time']);
        });
    }

    /**
     * As mentioned by Bhanu to avoid confusion between Closed and Resolved
     * statuses we are removing Resolved form the system as it does not have any
     * specification in the system and as statuses are Dynamic users can always create
     * status with "Resolved" in their system.
     *
     * This funciton removes Resolved status only if it has not been applied to any ticket
     * anytime to ensure if we remove the status while updating old system it does not create
     * any issue
     *
     */
    private function removeResolvedStatus()
    {
        if(!in_array(Config::get('app.env'), ["development", "testing"])) {
           $resolved = Ticket_Status::where('name', 'Resolved')->first();
           if($resolved) {
               $ticketsWereInReslovedAnyTime = TicketActivityLog::where('field', 'status')->where('value', $resolved->id)->count();
                $ticketsAreInResolved = Tickets::where('status', $resolved->id)->count();
                if (!($ticketsWereInReslovedAnyTime + $ticketsAreInResolved)) {
                    $resolved->delete();
                }
           }
        }
    }
}