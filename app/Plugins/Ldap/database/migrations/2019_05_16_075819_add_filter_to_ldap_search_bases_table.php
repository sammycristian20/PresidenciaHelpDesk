<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFilterToLdapSearchBasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ldap_search_bases', function (Blueprint $table) {
          $table->string('filter', 400);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ldap_search_bases', function (Blueprint $table) {
          $table->dropColumn('filter');
        });
    }
}
