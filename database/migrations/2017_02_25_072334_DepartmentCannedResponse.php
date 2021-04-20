<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DepartmentCannedResponse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_canned_resposne', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dept_id')->unsigned();
            $table->foreign('dept_id')->references('id')->on('department');
            $table->integer('canned_id')->unsigned();
            $table->foreign('canned_id')->references('id')->on('canned_response');
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
        Schema::table('department_canned_resposne', function (Blueprint $table) {
            Schema::drop('department_canned_resposne');
        });
    }
}
