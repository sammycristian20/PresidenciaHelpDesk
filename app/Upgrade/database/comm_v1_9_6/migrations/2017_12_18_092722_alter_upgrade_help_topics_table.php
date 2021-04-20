<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUpgradeHelpTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::disableForeignKeyConstraints();
        Schema::table('help_topic', function (Blueprint $table) {
            $table->dropForeign('help_topic_ibfk_4');
            $table->dropForeign('help_topic_ibfk_5');
            $table->dropForeign('help_topic_ibfk_6');
            $table->dropColumn(['priority', 'sla_plan', 'auto_assign']);
            $table->longText('nodes')->default(null)->nullable();
            $table->string('linked_departments', 5000)->default(null)->nullable();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
