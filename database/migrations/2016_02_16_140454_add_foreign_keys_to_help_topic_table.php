<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToHelpTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_topic', function (Blueprint $table) {
            // $table->foreign('custom_form', 'help_topic_ibfk_1')->references('id')->on('custom_forms')->onUpdate('NO ACTION')->onDelete('RESTRICT');
            $table->foreign('department', 'help_topic_ibfk_2')->references('id')->on('department')->onUpdate('NO ACTION')->onDelete('RESTRICT');
            // $table->foreign('ticket_status', 'help_topic_ibfk_3')->references('id')->on('ticket_status')->onUpdate('NO ACTION')->onDelete('RESTRICT');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::table('help_topic', function (Blueprint $table) {
            // $table->dropForeign('help_topic_ibfk_1');
            $table->dropForeign('help_topic_ibfk_2');
            // $table->dropForeign('help_topic_ibfk_3');
            
        });
    }
}
