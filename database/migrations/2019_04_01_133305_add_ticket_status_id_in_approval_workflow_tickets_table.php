<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * added new column ticket_status_id to store old ticket status id
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AddTicketStatusIdInApprovalWorkflowTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approval_workflow_tickets', function (Blueprint $table) {
            $table->integer('ticket_status_id')->unsigned()->nullable();
        });

        Schema::table('approval_workflow_tickets', function (Blueprint $table) {
            $table->foreign('ticket_status_id')->references('id')->on('ticket_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approval_workflow_tickets', function (Blueprint $table) {
            $table->dropForeign('ticket_status_id');
            $table->dropColumn('ticket_status_id');
        });
    }
}

