<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('log_category_id')->unsigned();

            // it has to be string because email messageId will also be stored here
            $table->string('referee_id');
            $table->string('referee_type', 255);
            $table->string('sender_mail');
            $table->string('reciever_mail');
            $table->string('subject');
            $table->string('body');
            $table->string('source');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_logs');
    }
}
