<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTasksTableToMakeTaskListIdNullable extends Migration
{
    public function __construct()
    {
        //adding this workaround beacuase modifying columns in a table with a enum column is not currently supported.
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
            $table->string('task_list_id')->nullable()->charset('')->collation('')->change();
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
            $table->string('task_list_id')->charset('')->collation('')->change();
        });
    }
}
