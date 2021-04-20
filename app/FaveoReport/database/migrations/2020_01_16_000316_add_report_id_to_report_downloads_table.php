<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportIdToReportDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_downloads', function (Blueprint $table) {
            $table->bigInteger("report_id")->after("file");

            // TODO: remove report type from the column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_downloads', function (Blueprint $table) {
            $table->dropColumn("report_id");
        });
    }
}
