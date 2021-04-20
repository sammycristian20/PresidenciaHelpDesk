<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHelptopicAndDepartmentColumnsToChatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat', function (Blueprint $table) {
            $table->integer('department');
            $table->integer('helptopic');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat', function (Blueprint $table) {
            $table->dropColumn('department');
            $table->dropColumn('helptopic');
        });
    }
}
