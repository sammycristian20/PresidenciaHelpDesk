<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTimeZoneAndTimeFarmatColumnInSettingsSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_system', function (Blueprint $table) {
            $table->renameColumn('time_zone', 'time_zone_id')->nullable()->change();
            $table->renameColumn('time_farmat', 'time_format');
        });

        Schema::table('settings_system', function (Blueprint $table) {
            $table->string('time_zone_id', 50)->nullable()->change();
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
            $table->renameColumn('time_zone_id', 'time_zone');
            $table->renameColumn('time_farmat', 'time_format');
        });

        Schema::table('settings_system', function (Blueprint $table) {
            $table->string('time_zone')->nullable(false)->change();
        });
    }
}
