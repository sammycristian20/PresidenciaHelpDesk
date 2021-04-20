<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BusinessScheduleOpenCustomTimeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('business_open_custom_time', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_schedule_id')->unsigned();
            $table->string('open_time');
            $table->string('close_time');
            $table->timestamps();
        });
        Schema::table('business_open_custom_time', function (Blueprint $table) {
            $table->foreign('business_schedule_id')->references('id')->on('business_schedule')->onUpdate('NO ACTION')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('business_open_custom_time');
//        Schema::table('sla_open_custom_time', function (Blueprint $table) {
//            $table->dropForeign('sla_schedule_info_id_idfk_1');
//        });
    }

}
