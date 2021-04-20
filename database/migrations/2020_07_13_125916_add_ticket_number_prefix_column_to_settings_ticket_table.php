<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketNumberPrefixColumnToSettingsTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_ticket', function (Blueprint $table) {
            $table->string('ticket_number_prefix', 20);
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
            $table->dropColumn('ticket_number_prefix');
        });
    }
}
