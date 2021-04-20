<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToKbSettings extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('kb_settings', function (Blueprint $table) {
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('kb_settings', function (Blueprint $table) {
             $table->dropColumn('status');
        });
    }
}
