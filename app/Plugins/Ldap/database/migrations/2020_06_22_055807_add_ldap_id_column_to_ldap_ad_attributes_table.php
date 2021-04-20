<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLdapIdColumnToLdapAdAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ldap_ad_attributes', function (Blueprint $table) {
            // get id of default LDAP if exists
            $table->bigInteger('ldap_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ldap_ad_attributes', function (Blueprint $table) {
            $table->dropColumn('ldap_id');
        });
    }
}
