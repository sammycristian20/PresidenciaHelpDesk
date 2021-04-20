<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskAssigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('task_assignees')) {
            Schema::create('task_assignees', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('task_id')->unsigned()->nullable(false);
                $table->integer('user_id')->unsigned()->nullable(false);
                $table->integer('team_id')->unsigned()->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_assignees');
    }
}
