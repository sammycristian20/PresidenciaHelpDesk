<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSettingsTicketTableToAddShowUserLocationColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_ticket', function (Blueprint $table) {
            $table->tinyInteger('show_user_location')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings_ticket', function (Blueprint $table) {
            $table->dropColumn('show_user_location');
        });
    }
}
