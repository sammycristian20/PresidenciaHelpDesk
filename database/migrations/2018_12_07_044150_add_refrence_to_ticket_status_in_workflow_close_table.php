<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRefrenceToTicketStatusInWorkflowCloseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workflow_close', function (Blueprint $table) {
            $table->integer('status')->unsigned()->change();
            $table->foreign('status')->references('id')->on('ticket_status')
            ->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workflow_close', function (Blueprint $table) {
            $table->dropForeign(['status']);
            $table->integer('status')->change();
        });
    }
}
