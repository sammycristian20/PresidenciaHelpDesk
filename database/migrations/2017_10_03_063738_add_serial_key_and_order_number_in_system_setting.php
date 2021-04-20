<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerialKeyAndOrderNumberInSystemSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_system', function (Blueprint $table) {
            $table->string('serial_key', 100)->nullable();
            $table->string('order_number', 100)->nullable();
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
            $table->dropColumn('serial_key');
            $table->dropColumn('order_number');
        });
    }
}
