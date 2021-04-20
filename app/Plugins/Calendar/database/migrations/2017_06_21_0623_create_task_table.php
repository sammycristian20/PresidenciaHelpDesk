<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        if (!Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->increments('id');
                $table->string('task_name', 255);
                $table->text('task_description');
                $table->datetime('task_start_date')->nullable();
                $table->datetime('task_end_date')->nullable();
                $table->integer('created_by')->references('id')->on('users');
                $table->enum('status', ['active', 'inactive', 'deleted'])->default('active');
                $table->boolean('is_private')->default(1);
                $table->integer('ticket_id')->unsigned()->nullable();
                $table->integer('parent_id')->unsigned()->nullable();
                $table->boolean('is_complete')->default(0);
                $table->datetime('due_alert');
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
        Schema::dropIfExists('tasks');
    }
}
