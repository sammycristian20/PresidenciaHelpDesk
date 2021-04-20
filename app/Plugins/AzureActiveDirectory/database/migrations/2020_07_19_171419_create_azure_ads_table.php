<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAzureAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('azure_ads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_name');
            $table->string('tenant_id');
            $table->string('app_id');
            $table->string('app_secret');
            $table->string('login_button_label')->nullable();
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
        Schema::dropIfExists('azure_ads');
    }
}
