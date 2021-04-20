<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLdapFaveoAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ldap_faveo_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('ad_attribute_id');
            $table->boolean('overwrite')->default(false);
            $table->boolean('editable')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ldap_faveo_attributes');
    }
}
