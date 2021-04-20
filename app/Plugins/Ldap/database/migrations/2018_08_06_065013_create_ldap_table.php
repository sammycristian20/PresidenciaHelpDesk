<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLdapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if(!Schema::hasTable('ldap')){
          Schema::create('ldap', function (Blueprint $table) {
              $table->increments('id');
              $table->string('domain');
              $table->string('username');
              $table->string('password');
              $table->timestamps();
          });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ldap');
    }
}
