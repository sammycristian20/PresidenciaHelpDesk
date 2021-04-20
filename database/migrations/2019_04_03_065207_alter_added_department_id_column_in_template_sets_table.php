<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * added new column department_id in template_sets table
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AlterAddedDepartmentIdColumnInTemplateSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template_sets', function (Blueprint $table) {
            $table->integer('department_id')->unsigned()->nullable();
            $table->foreign('department_id')->references('id')->on('department');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_sets', function (Blueprint $table) {
            $table->dropForeign('department_id');
            $table->dropColumn('department_id');
        });
    }
}
