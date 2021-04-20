<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWhatsappTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('sid');
            $table->string('token');
            $table->string('business_phone');
            $table->string('webhook_url')->nullable();
            $table->string('is_image_inline')->nullable();
            $table->integer('new_ticket_interval')->default(1);
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
        Schema::dropIfExists('whatsapp');
    }
}
