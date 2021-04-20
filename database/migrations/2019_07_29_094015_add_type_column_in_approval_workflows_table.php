<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * added new column type in approval_workflows table
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AddTypeColumnInApprovalWorkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approval_workflows', function (Blueprint $table) {
            $table->string('type', 30);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approval_workflows', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}