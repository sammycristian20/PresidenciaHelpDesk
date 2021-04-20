<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketActionEmailUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_action_email_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_action_email_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });
        // Schema::table('ticket_action_email_user', function($table) {
        //   $table->foreign('ticket_action_email_id')->references('id')
        //                                ->on('ticket_action_emails');
        // });
        // Schema::table('ticket_action_email_user', function($table) {
        //   $table->foreign('user_id')->references('id')
        //                                ->on('users');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_action_email_user');
    }
}
