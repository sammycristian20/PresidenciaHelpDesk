<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('template_id');
            $table->string('name');
            $table->string('assignees');
            $table->string('end');
            $table->string('end_unit');
            $table->string('order');
            $table->foreign('template_id')->references('id')->on('task_templates')
                ->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('template_tasks');
    }
}
