<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelephonyProviderSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telephony_provider_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('short', 50);
            $table->string('app_id')->nullable();
            $table->string('token')->nullable();
            $table->string('iso',5)->default('IN');
            $table->boolean('log_miss_call')->default(0);
            $table->smallInteger('conversion_waiting_time')->default(0);
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
        Schema::drop('telephony_provider_settings');
    }
}
