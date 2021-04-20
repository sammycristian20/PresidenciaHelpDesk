<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeRepeatAlertsToNullableForTasksAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks_alerts', function (Blueprint $table) {
            $table->dropColumn('repeat_alerts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks_alerts', function (Blueprint $table) {
            $table->enum('repeat_alerts', ['never', 'daily', 'weekly', 'monthly', 'never'])->default('daily');
        });
    }
}
