<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrgDeptManagerToOrganizationDept extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organization_dept', function (Blueprint $table) {
            $table->integer('org_dept_manager')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organization_dept', function (Blueprint $table) {
            $table->dropColumn('org_dept_manager');
        });
    }
}
