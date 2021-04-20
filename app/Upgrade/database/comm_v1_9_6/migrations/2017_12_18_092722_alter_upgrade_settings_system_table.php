<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUpgradeSettingsSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('settings_system', function (Blueprint $table) {
            $table->dropForeign('settings_system_ibfk_1');
            $table->dropForeign('settings_system_ibfk_4');
        });
    
        Schema::table('settings_system', function (Blueprint $table) {
            $table->string('time_zone', 50)->change();
            $table->string('date_time_format', 50)->change();
            $table->string('serial_key', 100);
            $table->string('order_number', 100);
         
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
