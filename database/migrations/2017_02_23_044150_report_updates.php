<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReportUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('resolution_time')->nullable();
            $table->renameColumn('last_response_at', 'first_response_time');
            $table->integer('is_response_sla');
            $table->integer('is_resolution_sla');
        });
         Schema::table('ticket_thread', function (Blueprint $table) {
            $table->string('response_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            //$table->dropColumn('resolution_time');
            //$table->dropColumn('last_response_at');
            //$table->dropColumn('is_response_sla');
           // $table->dropColumn('is_resolution_sla');
            //$table->dropColumn('response_time');
        });
    }
}
