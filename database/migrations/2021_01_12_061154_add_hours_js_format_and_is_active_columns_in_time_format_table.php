<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * adding is_active column in time_format table
 * So, that few entries of this table could be made deactive
 * So, that it won't affect in time format dependency in system setting module
 */
class AddHoursJsFormatAndIsActiveColumnsInTimeFormatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_format', function (Blueprint $table) {
            $table->string('hours', 14);
            $table->string('js_format', 14);
            $table->boolean('is_active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_format', function (Blueprint $table) {
            $table->dropColumn('hours');
            $table->dropColumn('js_format');
            $table->dropColumn('is_active');
        });
    }
}

