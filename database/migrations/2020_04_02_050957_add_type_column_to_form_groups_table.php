<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Added group_type column in form_groups table to separate helpdesk and plugin forms
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AddTypeColumnToFormGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_groups', function (Blueprint $table) {
            $table->string('group_type',25)->default('ticket');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_groups', function (Blueprint $table) {
            $table->dropColumn('group_type');
        });
    }
}
