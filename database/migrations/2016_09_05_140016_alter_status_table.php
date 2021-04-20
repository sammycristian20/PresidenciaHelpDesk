<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// use App\Model\helpdesk\Ticket\Ticket_Status;

class AlterStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_status', function (Blueprint $table) {
            $table->boolean('visibility_for_client');
            $table->boolean('allow_client');
            $table->boolean('visibility_for_agent');
            $table->integer('purpose_of_status');
            $table->integer('secondary_status')->nullable();
            $table->integer('send_email');
            $table->integer('halt_sla');
            $table->integer('order');
            $table->string('icon');
            $table->string('icon_color');
            $table->integer('default')->nullable();
            $table->dropColumn('state');
            $table->dropColumn('mode');
            $table->dropColumn('flags');
            $table->dropColumn('sort');
            $table->dropColumn('email_user');
            $table->dropColumn('icon_class');
            $table->dropColumn('properties');
//            to do
//            add code to update to new database
//            Ticket_Status::where()
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
            $table->dropIfExists('visibility_for_client');
            $table->dropIfExists('allow_client');
            $table->dropIfExists('visibility_for_agent');
            $table->dropIfExists('purpose_of_status');
            $table->dropIfExists('secondary_status');
            $table->dropIfExists('send_email');
            $table->dropIfExists('halt_sla');
            $table->dropIfExists('order');
            $table->dropIfExists('icon');
            $table->dropIfExists('icon_color');
            $table->dropIfExists('default');
           

        });
    }
}
