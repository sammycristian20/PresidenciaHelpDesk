<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_tracks', function (Blueprint $table) {
            $table->increments('id');
            $table->text('description');
            $table->integer('work_time');
            $table->integer('ticket_id')->unsigned();
            $table->foreign('ticket_id')->references('id')->on('tickets');
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
        Schema::dropIfExists('time_tracks');
    }
}
