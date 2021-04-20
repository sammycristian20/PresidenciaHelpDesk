<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketListenersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_listeners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('triggered_by');
            $table->boolean('status');
            $table->integer('order');
            $table->string('matcher');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_listeners');
    }
}
