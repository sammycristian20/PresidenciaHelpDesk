<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalLevelApproversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_level_approvers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('approval_level_id')->unsigned();
            $table->integer('approval_level_approver_id')->unsigned();
            $table->string('approval_level_approver_type');
        });

        Schema::table('approval_level_approvers', function($table) {
            $table->foreign('approval_level_id')->references('id')->on('approval_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_level_approvers');
    }
}
