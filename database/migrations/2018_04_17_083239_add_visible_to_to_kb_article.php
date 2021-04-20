<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVisibleToToKbArticle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kb_article', function($table) {
        $table->string('visible_to')->default('all_users');;
        $table->integer('author');

         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kb_article', function($table) {
        $table->dropColumn('visible_to');
         $table->dropColumn('author');
    });
    }
}
