<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCommentEnabledToKbSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kb_settings', function (Blueprint $table) {
            $table->boolean('is_comment_enabled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kb_settings', function (Blueprint $table) {
            $table->dropColumn('is_comment_enabled');
        });
    }
}
