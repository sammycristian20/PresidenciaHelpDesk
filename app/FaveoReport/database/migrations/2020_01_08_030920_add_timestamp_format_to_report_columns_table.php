<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampFormatToReportColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_columns', function (Blueprint $table) {
            $table->string('timestamp_format')->nullable()->after("is_timestamp");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_columns', function (Blueprint $table) {
            $table->dropColumn('timestamp_format');
        });
    }
}
