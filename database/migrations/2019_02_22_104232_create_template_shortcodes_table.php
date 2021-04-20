<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateShortcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_shortcodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key_name', 50);
            $table->string('shortcode', 100);
            $table->string('description_lang_key', 255);
            $table->string('plugin_name', 50)->nullable();
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
        Schema::dropIfExists('template_shortcodes');
    }
}
