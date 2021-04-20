<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappWebhookStore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp_webhook_store', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('MediaContentType0');
            $table->string('SmsMessageSid');
            $table->string('NumMedia');
            $table->string('From');
            $table->string('MediaUrl0');
            $table->text('Body')->nullable();
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
        Schema::dropIfExists('whatsapp_webhook_store');
    }
}
