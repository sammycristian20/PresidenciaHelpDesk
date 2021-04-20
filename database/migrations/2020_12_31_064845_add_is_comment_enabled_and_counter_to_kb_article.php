<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCommentEnabledAndCounterToKbArticle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kb_article', function (Blueprint $table) {
            $table->boolean('is_comment_enabled')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kb_article', function (Blueprint $table) {
            $table->dropColumn('is_comment_enabled');
        });
    }
}
