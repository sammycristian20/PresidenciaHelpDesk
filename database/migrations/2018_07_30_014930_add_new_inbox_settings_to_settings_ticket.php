<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewInboxSettingsToSettingsTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_ticket', function($table) {
            $table->boolean('count_internal')->default(0);
            $table->boolean('show_status_date')->default(0);
            $table->boolean('show_org_details')->default(0);
            $table->text('custom_field_name')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings_ticket', function($table) {
            $table->dropColumn('count_internal');
            $table->dropColumn('show_status_date');
            $table->dropColumn('show_org_details');
            $table->dropColumn('custom_field_name');
        });
    }
}
