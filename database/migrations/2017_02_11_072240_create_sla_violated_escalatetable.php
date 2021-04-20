<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlaViolatedEscalatetable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('sla_violated_escalate', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sla_plan');
            $table->string('escalate_time');
            $table->string('escalate_type');
            $table->string('escalate_person');
           
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
        Schema::drop('sla_violated_escalate');
    }
}
