<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEncryptionAndPortToLdapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ldap', function (Blueprint $table) {
          $table->string('encryption', 10)->nullable();
          $table->integer('port')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ldap', function (Blueprint $table) {
            $table->dropColumn('encryption');
            $table->dropColumn('port');
        });
    }
}
