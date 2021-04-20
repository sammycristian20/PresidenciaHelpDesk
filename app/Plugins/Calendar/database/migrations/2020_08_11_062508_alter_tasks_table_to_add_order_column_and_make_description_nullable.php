<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTasksTableToAddOrderColumnAndMakeDescriptionNullable extends Migration
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
            $table->integer('order')->nullable();
            $table->string('task_description')->nullable()->change();
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
            $table->dropColumn('order');
            $table->string('task_description')->nullable(false)->change();
        });
    }
}
