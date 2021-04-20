<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListenersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listeners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('status');
            $table->string('description');
            $table->string('performed_by');
            $table->integer('order');
            $table->string('rule_match');
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
        Schema::dropIfExists('listeners');
    }
}
