<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sender_id');
            $table->string('message_id');
            $table->string('page_id');
            $table->text('message')->nullable();
            $table->text('attachment_urls');
            $table->text('posted_at');
            $table->integer('ticket_id')->nullable();
            $table->integer('processed')->default(0);
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
        Schema::dropIfExists('facebook_messages');
    }
}
