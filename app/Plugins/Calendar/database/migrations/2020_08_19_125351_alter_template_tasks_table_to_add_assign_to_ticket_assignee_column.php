<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTemplateTasksTableToAddAssignToTicketAssigneeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template_tasks', function (Blueprint $table) {
            $table->integer('assign_task_to_ticket_agent')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_tasks', function (Blueprint $table) {
            $table->dropColumn('assign_task_to_ticket_agent');
        });
    }
}
