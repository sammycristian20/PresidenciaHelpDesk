<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTicketWorkflowsTableToMakeInternalNoteColumnTextFromVarchar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_workflows', function (Blueprint $table) {
            $table->text('internal_notes')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_workflows', function (Blueprint $table) {
            $table->string('internal_notes')->change();
        });
    }
}
