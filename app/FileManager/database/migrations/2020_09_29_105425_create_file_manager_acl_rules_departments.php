<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileManagerAclRulesDepartments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('file_manager_acl_rules_departments')) {
            Schema::create('file_manager_acl_rules_departments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('rule_id')->nullable();
                $table->unsignedInteger('department_id')->nullable();
                $table->timestamps();

                $table->foreign('department_id')
                    ->references('id')->on('department');

                $table->foreign('rule_id')
                    ->references('id')->on('file_manager_acl_rules');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_manager_acl_rules_departments');
    }
}
