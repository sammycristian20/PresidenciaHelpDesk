<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAdAttributeIdToMappedToInLdapFaveoAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ldap_faveo_attributes', function (Blueprint $table) {
            $table->renameColumn('ad_attribute_id', 'mapped_to');
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
            $table->renameColumn('mapped_to', 'ad_attribute_id');
        });
    }
}
