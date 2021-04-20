<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLdapSearchBasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('ldap_search_bases', function (Blueprint $table)
          {
              $table->increments('id');

              $table->integer('ldap_id');

              $table->string('search_base');

              $table->string('user_type', 30);

              //as comma seperated value which will be converted into array as attribute
              $table->string('department_ids');

              $table->string('organization_ids');

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
        Schema::dropIfExists('ldap_search_bases');
    }
}
