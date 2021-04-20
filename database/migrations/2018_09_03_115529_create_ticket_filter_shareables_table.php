<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketFilterShareablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_filter_shareables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_filter_id')->unsigned();
            $table->integer('ticket_filter_shareable_id')->unsigned();
            $table->string('ticket_filter_shareable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_filter_shareables');
    }
}
