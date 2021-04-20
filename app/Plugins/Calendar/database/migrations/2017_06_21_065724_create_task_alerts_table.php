<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('tasks_alerts')) {
            Schema::create('tasks_alerts', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('task_id')->unsigned()->nullable(false);
                $table->enum('repeat_alerts', ['never', 'daily', 'weekly', 'monthly', 'never'])->default('daily');
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
        Schema::dropIfExists('tasks_alerts');
    }
}
