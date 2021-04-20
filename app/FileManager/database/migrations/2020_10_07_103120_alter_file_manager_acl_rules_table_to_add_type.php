<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFileManagerAclRulesTableToAddType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_manager_acl_rules', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->string('dirname')->nullable();
            $table->string('basename')->nullable();
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
            $table->dropColumn(['type','dirname','basename']);
        });
    }
}
