<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFileManagerAclRulesToAddVisibilityColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_manager_acl_rules', function (Blueprint $table) {
            $table->boolean('hidden')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_manager_acl_rules', function (Blueprint $table) {
            $table->dropColumn('hidden');
        });
    }
}
