<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSchemaPrefixAndSuffixToLdapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ldap', function (Blueprint $table) {
          $table->string('schema', 30);
          $table->string('prefix', 100);
          $table->string('suffix', 100);
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
          $table->dropColumn('schema');
          $table->dropColumn('prefix');
          $table->dropColumn('suffix');
        });
    }
}
