<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDueAlertAndAlertRepeatTextToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->integer('task_list_id' );
            $table->text('due_alert_text')->nullable();
            $table->text('alert_repeat_text')->nullable();
            $table->datetime('due_alert')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('task_list_id');
            $table->dropColumn('due_alert_text');
            $table->dropColumn('alert_repeat_text');
            $table->datetime('due_alert')->nullable(false)->change();
        });
    }
}
