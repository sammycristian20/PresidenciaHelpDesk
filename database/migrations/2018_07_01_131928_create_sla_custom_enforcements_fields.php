<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlaCustomEnforcementsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sla_custom_enforcements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('f_name');
            $table->string('f_type');
            $table->string('f_value');
            $table->string('f_label');
            $table->integer('sla_id')->unsigned();
            $table->foreign('sla_id')->references('id')->on('ticket_status');
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
        Schema::dropIfExists('sla_custom_enforcements');
    }
}
