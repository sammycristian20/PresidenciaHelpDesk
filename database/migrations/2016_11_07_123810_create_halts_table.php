<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHaltsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('halts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_id');
            $table->timestamp('halted_at');
            $table->integer('time_used');
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
        Schema::drop('halts');
    }
}
