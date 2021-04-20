<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDefaultColumnToLdapAdAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ldap_ad_attributes', function (Blueprint $table) {
            $table->boolean('is_default')->default(true);
            $table->boolean('is_loginable')->default(false);
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
            $table->dropColumn('is_default');
            $table->dropColumn('is_loginable');
        });
    }
}
