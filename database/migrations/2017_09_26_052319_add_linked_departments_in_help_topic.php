<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLinkedDepartmentsInHelpTopic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_topic', function (Blueprint $table) {
            $table->string('linked_departments', 5000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('help_topic', function (Blueprint $table) {
            $table->dropColumn('linked_departments');
        });
    }
}
