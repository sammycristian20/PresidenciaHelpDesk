<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomRuleColumnMakeNullableInWorflowAction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workflow_action', function (Blueprint $table) {
            $table->longText('custom_action')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workflow_action', function (Blueprint $table) {
            $table->longText('custom_action')->nullable(false)->change();
        });
    }
}
