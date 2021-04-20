<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketFilterMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_filter_meta', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_filter_id')->unsigned();
            $table->string('key', 255);
            $table->longText('value');
        });

        Schema::table('ticket_filter_meta', function (Blueprint $table) {
            $table->foreign('ticket_filter_id')->references('id')->on('ticket_filters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('ticket_filter_meta');
        Schema::enableForeignKeyConstraints();
    }
}
