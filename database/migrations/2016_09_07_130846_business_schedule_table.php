<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BusinessScheduleTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('business_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_hours_id')->unsigned();
            $table->string('days');
            $table->string('status');
            $table->timestamps();
        });

        Schema::table('business_schedule', function (Blueprint $table) {
            $table->foreign('business_hours_id')->references('id')->on('business_hours')->onUpdate('NO ACTION')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('business_schedule');
//        Schema::table('business_schedule', function (Blueprint $table) {
//            $table->dropForeign('schedules_id_idfk_1');
//        });
    }

}
