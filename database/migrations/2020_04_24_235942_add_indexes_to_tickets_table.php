<?php

use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Ticket\Tickets;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // NOTE FROM AVINASH: adding foreign key constraint for status because its the most used one in navigation
        $this->sanitizeTicketsTable();

        // these migrations might break for very old clients, so adding try-catch to avoid any unexpected result in auto-update
        // even if these migrations don't run, it won't break anything, it is just for better performance
        // it has been build as bigInt, but not really needed to be
        // for new clients, it is already exists so it will have no effect
        try{
            Schema::table('tickets', function (Blueprint $table) {
                $table->integer("status")->unsigned()->change();
            });
        } catch (Exception $e){
                //ignore
        }

        try{
            Schema::table('tickets', function (Blueprint $table) {

                // duedate is used in most of the calculations and queries in reports and inbox page. Good to be indexed
                $table->index('duedate');

                // will improve performance while searching for regex for ticket number. Will decrease chances of ticket number duplication
                $table->index('ticket_number');

                // for new clients, it already int, but for older clients, it was big int
                $table->foreign('status')->references('id')->on('ticket_status');

            });
        } catch (Exception $e){
            // ignore
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign('status');
            $table->dropIndex('duedate');
            $table->dropIndex('ticket_number');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign('status');
            $table->dropIndex('duedate');
        });
    }

    /**
     * Santizes ticket table for anything which blocks foriegn key creation
     */
    private function sanitizeTicketsTable()
    {
        // there are cases where status in ticket table doesn't exist in status table.
        // Making status of those tickets as trash
        $statusIds = Ticket_Status::pluck('id')->toArray();

        // id of deleted status
        $deletedStatusId = Ticket_Status::whereHas('type', function($q){
            $q->where('name', 'deleted');
        })->value('id');

        Tickets::whereNotIn('status', $statusIds)->update(['status' => $deletedStatusId]);
    }
}
