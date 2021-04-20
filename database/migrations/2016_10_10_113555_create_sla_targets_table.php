<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlaTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         Schema::create('sla_targets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('sla_id');
            $table->integer('priority_id');
            $table->string('respond_within');
            $table->string('resolve_within');
             $table->integer('business_hour_id');
            $table->integer('send_email');
            $table->boolean('send_sms')->default(0);
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
        Schema::drop('sla_targets');
    }
}
