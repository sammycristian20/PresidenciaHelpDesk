<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOverwritableToLdapFaveoAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ldap_faveo_attributes', function (Blueprint $table) {
          $table->boolean('overwriteable')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ldap_faveo_attributes', function (Blueprint $table) {
          $table->dropColumn('overwriteable');
        });
    }
}
