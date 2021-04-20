<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwitterAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('twitter_app')) {
            Schema::create('twitter_app', function (Blueprint $table) {
                $table->increments('id');
                $table->string('consumer_api_key');
                $table->string('consumer_api_secret');
                $table->string('access_token');
                $table->string('access_token_secret');
                $table->integer('reply_interval')->default(5);
                $table->string('hashtag_text')->nullable();
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
        //
    }
}
