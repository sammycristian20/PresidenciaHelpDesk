<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoogle2faColumnToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('google2fa_secret')->nullable();
            $table->timestamp('google2fa_activation_date')->nullable();
            $table->boolean('is_2fa_enabled')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google2fa_secret');
            $table->dropColumn('google2fa_activation_date');
            $table->dropColumn('is_2fa_enabled');
        });
    }
}
