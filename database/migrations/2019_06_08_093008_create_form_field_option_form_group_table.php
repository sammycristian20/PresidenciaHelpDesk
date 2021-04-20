<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormFieldOptionFormGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_field_option_form_group', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedInteger('form_group_id');
          $table->unsignedInteger('form_field_option_id');
          $table->integer('sort_order');

          $table->foreign('form_group_id')
               ->references('id')
               ->on('form_groups')
               ->onUpdate('cascade')
               ->onDelete('cascade');

          $table->foreign('form_field_option_id')
              ->references('id')
              ->on('form_field_options')
              ->onUpdate('cascade')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_field_option_form_group');
    }
}
