<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_filters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->boolean('status');
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('ticket_filters', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('ticket_filters');
        Schema::enableForeignKeyConstraints();
    }
}
