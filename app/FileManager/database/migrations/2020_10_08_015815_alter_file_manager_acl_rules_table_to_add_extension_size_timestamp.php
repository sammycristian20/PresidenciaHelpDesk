<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFileManagerAclRulesTableToAddExtensionSizeTimestamp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_manager_acl_rules', function (Blueprint $table) {
            $table->string('extension')->nullable();
            $table->string('size')->nullable();
            $table->string('timestamp');
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
            $table->dropColumn(['extension','size','timestamp']);
        });
    }
}
