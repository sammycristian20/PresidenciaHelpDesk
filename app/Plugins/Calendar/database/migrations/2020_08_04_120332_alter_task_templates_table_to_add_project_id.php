<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTaskTemplatesTableToAddProjectId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_templates', function (Blueprint $table) {
            $table->dropColumn('template_body');
            $table->dropColumn('task_list_id');
            $table->integer('project_id');
            $table->string('name');
            $table->text('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_templates', function (Blueprint $table) {
            $table->string('template_body');
            $table->unsignedInteger('task_list_id');
            $table->dropColumn('project_id');
            $table->dropColumn('name');
            $table->dropColumn('description');
        });
    }
}
