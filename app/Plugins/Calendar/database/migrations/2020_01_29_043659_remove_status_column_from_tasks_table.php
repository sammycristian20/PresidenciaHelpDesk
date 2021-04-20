<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveStatusColumnFromTasksTable extends Migration
{
    /**
     * RemoveStatusColumnFromTasksTable constructor.
     */
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
            $table->dropColumn('status');
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
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active')->charset('')->collation('');
        });
    }
}
