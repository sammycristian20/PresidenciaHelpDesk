<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatetimeToKbSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kb_settings', function($table) {
            $table->string('date_format')->default('MMMM Do YYYY, h:mm:ss a');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kb_settings', function($table) {
            $table->dropColumn('date_format'); 
        });
    }
}
