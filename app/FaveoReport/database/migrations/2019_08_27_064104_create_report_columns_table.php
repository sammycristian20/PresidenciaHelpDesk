<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_columns', function (Blueprint $table) {
            // in case of custom, key is the key in which data will be sent
            // and value will the the label. If it is custom, it has to be linked
            // with some kind of equation
            $table->bigIncrements('id');
            $table->string('key');
            $table->string('label');
            $table->boolean('is_visible')->default(false);
            $table->boolean('is_sortable')->default(false);
            $table->boolean('is_timestamp')->default(false);
            $table->boolean('is_html')->default(false);
            $table->boolean('is_custom')->default(false);
            $table->string('equation');
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
        Schema::dropIfExists('report_columns');
    }
}
