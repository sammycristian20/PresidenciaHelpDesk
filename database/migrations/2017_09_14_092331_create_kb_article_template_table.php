<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKbArticleTemplateTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kb_article_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('status');
            $table->text('description', 65535);
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
        Schema::drop('kb_article_template');
    }
}
