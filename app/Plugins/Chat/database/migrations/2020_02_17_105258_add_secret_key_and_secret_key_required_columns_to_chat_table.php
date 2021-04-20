<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecretKeyAndSecretKeyRequiredColumnsToChatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat', function (Blueprint $table) {
            $table->string('secret_key')->nullable();
            $table->integer('secret_key_required')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat', function (Blueprint $table) {
            $table->dropColumn('secret_key');
            $table->dropColumn('secret_key_required');
        });
    }
}
