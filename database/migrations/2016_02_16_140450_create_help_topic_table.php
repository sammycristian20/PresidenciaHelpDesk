<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHelpTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_topic', function (Blueprint $table) {
            $table->increments('id');
            $table->string('topic');
            $table->string('parent_topic');
            $table->integer('custom_form')->unsigned()->nullable();
            $table->integer('department')->unsigned()->nullable();
            $table->integer('ticket_status')->unsigned()->nullable();
            $table->string('thank_page');
            $table->string('ticket_num_format');
            $table->string('internal_notes');
            $table->boolean('status');
            $table->boolean('type');
            $table->boolean('auto_response');
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
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop('help_topic');
    }
}
