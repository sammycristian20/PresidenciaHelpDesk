<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * added new column comment to ticket_status table for making comment required or optional
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AlterAddedCommentColumnToTicketStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_status', function (Blueprint $table) {
            $table->integer('comment')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_status', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
}
