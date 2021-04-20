<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRepeatAlertsColumnToTasksAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks_alerts', function (Blueprint $table) {
            $table->string('repeat_alerts')->nullable();
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
            $table->dropColumn('repeat_alerts');
        });
    }
}
