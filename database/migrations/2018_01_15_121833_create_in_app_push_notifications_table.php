<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInAppPushNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_app_push_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subscribed_user_id');
            $table->string('message');
            $table->string('url');
            $table->enum('status', ['pending', 'failed', 'delivered'])->default('pending');
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
        Schema::dropIfExists('in_app_push_notifications');
    }
}
