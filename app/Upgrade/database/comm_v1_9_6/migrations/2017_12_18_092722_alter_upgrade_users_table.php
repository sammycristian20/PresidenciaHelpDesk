<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUpgradeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('fcm_token')->default(null)->nullable();
            $table->string('i_token')->default(null)->nullable();
            $table->string('user_language', 10)->default(null)->nullable();
            $table->string('mobile_verify')->default(null)->nullable();
            $table->string('email_verify')->default(null)->nullable();
            $table->string('location');
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
