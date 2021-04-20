<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalWorkflowTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_workflow_tickets', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('approval_workflow_id')->unsigned();

            $table->string('name', 255);

            $table->integer('user_id')->unsigned();

            $table->integer('ticket_id')->unsigned();

            $table->string('status');

            $table->timestamps();
        });

        Schema::table('approval_workflow_tickets', function($table) {

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');;

            $table->foreign('approval_workflow_id')->references('id')->on('approval_workflows')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_workflow_tickets');
    }
}
