<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * added new columns type, user_id, name and execution_time
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AddTypeUserIdNameAndExecutionTimeColumnsInRecursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recurs', function (Blueprint $table) {
            $table->string('type')->default('admin_panel');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('name');
            $table->time('execution_time')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recurs', function (Blueprint $table) {
            $table->dropForeign('user_id');
            $table->dropColumn('type');
            $table->dropColumn('user_id');
            $table->dropColumn('name');
            $table->dropColumn('execution_time');
        });
    }
}
