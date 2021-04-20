<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTicketThreadTableResponseTimeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_thread', function (Blueprint $table) {
            $table->integer('response_time')->charset('')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_thread', function (Blueprint $table) {
            $table->string('response_time')->nullable()->change();
        });
    }
}
