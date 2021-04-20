<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMatchRuleColumnInWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workflow_name', function (Blueprint $table) {
            $table->string('rule_match', 10)->default('all');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

         Schema::table('workflow_name', function (Blueprint $table) {
            $table->dropColumn('rule_match');
        });
    }
}
