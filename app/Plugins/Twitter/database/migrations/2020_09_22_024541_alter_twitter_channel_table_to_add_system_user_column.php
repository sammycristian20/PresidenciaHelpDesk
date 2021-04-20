<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTwitterChannelTableToAddSystemUserColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twitter_channel', function (Blueprint $table) {
            $table->string('system_twitter_user')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twitter_channel', function (Blueprint $table) {
            $table->dropColumn('system_twitter_user');
        });
    }
}
