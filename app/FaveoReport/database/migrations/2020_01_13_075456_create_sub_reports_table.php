<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger("report_id");
            $table->string("identifier", 100);
            $table->string("data_type", 30)->nullable();
            $table->string("data_widget_url")->nullable();
            $table->string("data_url")->nullable();
            $table->string("selected_chart_type", 30)->nullable();
            $table->text("list_view_by")->nullable();
            $table->string("selected_view_by")->nullable();
            $table->string("add_custom_column_url")->nullable();
            $table->string("layout", 10)->default("n*1");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_reports');
    }
}
