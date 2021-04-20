<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration class to add new columns to `condition` table
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 * @since  v1.9.44
 */
class AddNewColumnsToConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conditions', function (Blueprint $table) {
            $table->string('icon', 30);
            $table->string('command', 255);
            $table->string('job_info', 100);
            $table->boolean('active')->default(1);
            $table->boolean('plugin_job')->default(0);
            $table->string('plugin_name', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conditions', function (Blueprint $table) {
            $table->dropColumn('icon');
            $table->dropColumn('command');
            $table->dropColumn('job_info');
            $table->dropColumn('active');
            $table->dropColumn('plugin_job');
        });
    }
}
