<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToCronLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cron_logs', function (Blueprint $table) {
            $table->string('status', 30)->nullable();
            // for storing reason/remark for cron success/failure
            $table->bigInteger('exception_log_id')->nullable();
            $table->dropColumn("log_category_id");
            $table->dropColumn("start_time");
            $table->renameColumn("message", "description");
            $table->string("command")->after("id");
            $table->bigInteger("duration")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cron_logs', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('exception_log_id');
            $table->integer('log_category_id')->unsigned();
            $table->string("start_time");
            $table->renameColumn("description", "message");
            $table->dropColumn("command");
            $table->dropColumn("duration");
        });
    }
}
