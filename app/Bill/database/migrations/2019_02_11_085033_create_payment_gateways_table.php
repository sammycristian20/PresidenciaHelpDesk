<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_payment_gateways', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('gateway_name', 50)->nullable();
            $table->string('key', 255)->nullable();
            $table->string('value', 255)->nullable();
            $table->boolean('status')->default(0);
            $table->boolean('is_default')->default(0);
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
        Schema::dropIfExists('payment_gateways');
    }
}
