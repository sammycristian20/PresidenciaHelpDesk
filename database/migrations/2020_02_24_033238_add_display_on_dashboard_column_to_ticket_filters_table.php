<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisplayOnDashboardColumnToTicketFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_filters', function (Blueprint $table) {
            $table->boolean("display_on_dashboard")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_filters', function (Blueprint $table) {
            $table->dropColumn("display_on_dashboard");
        });
    }
}
