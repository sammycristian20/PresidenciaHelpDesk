<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTaskTemplatesTableToAddCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_templates', function (Blueprint $table) {
            $table->dropColumn('project_id');

            $table->unsignedBigInteger('category_id')->nullable();

            $table->foreign('category_id')->references('id')
                ->on('task_categories')
                ->onDelete('set null'); //since category_id is nullable attribute so setting to null
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
            $table->integer('project_id');

            $table->dropColumn('category_id');

            $table->dropForeign(['category_id']);
        });
    }
}
