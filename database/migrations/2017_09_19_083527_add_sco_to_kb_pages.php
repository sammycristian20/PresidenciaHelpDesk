<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScoToKbPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('kb_pages', function($table) {
       
          $table->string('sco_title');
          $table->string('meta_description');
        
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('kb_pages', function($table) {
         //$table->dropColumn('sco_title');
         $table->dropColumn('meta_description');
        
       });
    }
}
