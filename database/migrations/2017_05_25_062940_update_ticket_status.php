<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTicketStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_status', function (Blueprint $table) {
            $table->string('send_email')->nullable()->change();
            $table->boolean('send_sms')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::table('ticket_status', function (Blueprint $table) {
            $table->dropIfExists('send_email');
            $table->dropIfExists('send_sms');
        });
    }
}
