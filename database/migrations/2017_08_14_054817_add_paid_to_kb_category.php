<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaidToKbCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kb_category', function($table) {
         $table->integer('display_order');
         $table->string('visible_to');
        
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kb_category', function($table) {
         $table->dropColumn('display_order');
         $table->dropColumn('visible_to');    
     });
    }
}
