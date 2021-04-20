<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFbAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_app', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app_id');
            $table->string('secret');
            $table->string('access_token');
            $table->integer('new_ticket_interval')->default(5);
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
        //
    }
}
