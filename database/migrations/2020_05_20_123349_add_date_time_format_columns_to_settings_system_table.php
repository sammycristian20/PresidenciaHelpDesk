<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateTimeFormatColumnsToSettingsSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_system', function (Blueprint $table) {
            $table->string('date_format', 50)->nullable()->default('F j, Y')->change();
            $table->string('time_farmat', 50)->nullable()->default('g:i a')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings_system', function (Blueprint $table) {
            $table->integer('time_farmat')->unsigned()->nullable()->index('time_farmat');
            $table->integer('date_format')->unsigned()->nullable()->index('date_format');
        });
    }
}
