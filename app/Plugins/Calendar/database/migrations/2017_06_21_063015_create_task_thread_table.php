<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskThreadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        if (!Schema::hasTable('tasks_threads')) {
            Schema::create('tasks_threads', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('system_note')->default(1);
                $table->integer('task_id')->unsigned()->nullable(false);
                $table->text('message');
                $table->integer('created_by')->unsigned()->nullable();
                $table->timestamps();
            });
        }
         \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks_threads');
    }
}
