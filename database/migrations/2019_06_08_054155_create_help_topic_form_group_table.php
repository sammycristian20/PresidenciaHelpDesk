<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHelpTopicFormGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_topic_form_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('form_group_id');
            $table->unsignedInteger('help_topic_id');
            $table->integer('sort_order');

            $table->foreign('form_group_id')
                 ->references('id')
                 ->on('form_groups')
                 ->onUpdate('cascade')
                 ->onDelete('cascade');

            $table->foreign('help_topic_id')
                ->references('id')
                ->on('help_topic')
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
        Schema::dropIfExists('help_topic_form_group');
    }
}
