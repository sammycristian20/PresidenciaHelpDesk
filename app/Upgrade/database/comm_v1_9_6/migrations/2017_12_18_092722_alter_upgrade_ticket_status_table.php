<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUpgradeTicketStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::table('ticket_status', function (Blueprint $table) {
            $table->dropColumn(['state', 'mode', 'flags', 'sort', 'email_user', 'icon_class', 'properties']);
            $table->boolean('visibility_for_client');
            $table->boolean('allow_client');
            $table->boolean('visibility_for_agent');
            $table->integer('purpose_of_status');
            $table->integer('secondary_status')->default(null)->nullable();
            $table->string('send_email')->default(null)->nullable();
            $table->integer('halt_sla');
            $table->integer('order');
            $table->string('icon');
            $table->string('icon_color');
            $table->integer('default')->default(null)->nullable();
            $table->boolean('send_sms')->default(0);
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
