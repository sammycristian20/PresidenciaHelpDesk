<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->integer('approval_workflow_id')->unsigned();
            $table->string('match', 10);
            $table->tinyInteger('order')->unsigned();
        });

        Schema::table('approval_levels', function($table) {
            $table->foreign('approval_workflow_id')->references('id')->on('approval_workflows');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_levels');
    }
}
