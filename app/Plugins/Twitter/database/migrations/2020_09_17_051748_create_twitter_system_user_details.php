<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitterSystemUserDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('twitter_system_user_details')) {
            Schema::create('twitter_system_user_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('user_id');
                $table->string('user_name');
                $table->string('screen_name');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitter_system_user_details');
    }
}
