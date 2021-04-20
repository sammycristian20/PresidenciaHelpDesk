<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketStatusAttachableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_status_attachables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_status_id')->unsigned();
            $table->integer('ticket_status_attachable_id')->unsigned();
            $table->string('ticket_status_attachable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_attachables');
    }
}
