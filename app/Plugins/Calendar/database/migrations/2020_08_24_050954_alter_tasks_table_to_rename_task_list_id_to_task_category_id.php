<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTasksTableToRenameTaskListIdToTaskCategoryId extends Migration
{
    public function __construct()
    {
        //adding this workaround because modifying columns in a table with a enum column is not currently supported.
        \DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('task_list_id', 'task_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('task_category_id', 'task_list_id');
        });
    }
}
