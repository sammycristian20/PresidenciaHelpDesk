<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInternalNotesToTicketWorkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('ticket_workflows', function($table) {
          $table->string('internal_notes');
          $table->string('target');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_workflows', function($table) {
            $table->dropColumn('internal_notes');
            $table->dropColumn('target');
        });
    }
}
