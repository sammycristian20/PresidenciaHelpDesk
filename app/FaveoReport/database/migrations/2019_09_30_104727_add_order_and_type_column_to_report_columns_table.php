<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderAndTypeColumnToReportColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_columns', function (Blueprint $table) {
            $table->integer('order')->default(0);
            $table->string('type', 150);
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
            $table->dropColumn('order');
            $table->dropColumn('type');
        });
    }
}
