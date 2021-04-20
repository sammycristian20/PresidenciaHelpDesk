<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFbChannelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_channel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('channel');//ex:twitter
            $table->string('via');//ex:message
            $table->string('message_id');
            $table->string('con_id')->nullable();
            $table->string('user_id');//from social media
            $table->string('ticket_id');
            $table->string('username');//from social media
            $table->string('page_access_token');
            $table->string('posted_at');//from social media
            $table->integer('hasExpired')->default(0);
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
        //
    }
}
