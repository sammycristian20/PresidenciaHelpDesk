<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Adds domain, api_key, secret and region to DB
 */
class AddServiceColumnsToEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->boolean('block_auto_generated');
            $table->string('domain')->nullbable();
            $table->string('key')->nullbable();
            $table->string('secret')->nullbable();
            $table->string('region')->nullbable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('block_auto_generated');
            $table->dropColumn('domain');
            $table->dropColumn('key');
            $table->dropColumn('secret');
            $table->dropColumn('region');    
        });
    }
}
