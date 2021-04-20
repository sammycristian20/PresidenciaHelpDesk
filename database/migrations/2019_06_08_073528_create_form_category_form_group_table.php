<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormCategoryFormGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_category_form_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('form_group_id');
            $table->unsignedInteger('form_category_id');
            $table->integer('sort_order');

            $table->foreign('form_group_id')
                 ->references('id')
                 ->on('form_groups')
                 ->onUpdate('cascade')
                 ->onDelete('cascade');

            $table->foreign('form_category_id')
                ->references('id')
                ->on('form_categories')
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
        Schema::dropIfExists('form_category_form_group');
    }
}
