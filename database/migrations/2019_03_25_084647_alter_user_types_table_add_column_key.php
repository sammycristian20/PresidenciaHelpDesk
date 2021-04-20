<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * added new column key to store name value , and make name value in camel case
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AlterUserTypesTableAddColumnKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_types', function (Blueprint $table) {
            $table->string('key', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('user_types', function (Blueprint $table) {
            $table->dropColumn('key');
        });
    }
}
