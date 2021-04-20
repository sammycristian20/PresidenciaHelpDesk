<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLabelVisibilityColumnToLdap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ldap', function (Blueprint $table){
            $table->string('ldap_label', 80);
            $table->string('forgot_password_link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ldap', function($table){
              $table->dropColumn('ldap_label');
              $table->dropColumn('forgot_password_link');
        });
    }
}
