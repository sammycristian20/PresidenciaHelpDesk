<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatedHalts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('halts', function (Blueprint $table) {
            $table->integer('halted_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('halts', function (Blueprint $table) {
           $table->dropColumn('halted_time')->nullable();
        });
    }
}
