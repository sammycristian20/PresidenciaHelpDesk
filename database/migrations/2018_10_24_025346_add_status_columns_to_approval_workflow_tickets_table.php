<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusColumnsToApprovalWorkflowTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('approval_workflow_tickets', function (Blueprint $table) {
        $table->integer('action_on_approve')->before('created_at');
        $table->integer('action_on_deny')->before('created_at');
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
        $table->dropColumn('action_on_approve');
        $table->dropColumn('action_on_deny');
      });
    }
}
