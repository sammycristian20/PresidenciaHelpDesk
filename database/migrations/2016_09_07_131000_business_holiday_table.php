<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BusinessHolidayTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('business_holidays', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('date');
            $table->integer('business_hours_id')->unsigned();
            $table->timestamps();
        });
        Schema::table('business_holidays', function (Blueprint $table) {
            $table->foreign('business_hours_id')->references('id')->on('business_hours')->onUpdate('NO ACTION')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('business_holidays');
    }

}
