<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImportIdentifierToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // check if ldap_unique_key exists in the table, If yes, just rename it to unique_key
            // if not, then create a new column unique_key
            // not writing it in LDAP plugin because it now a general import functionality which LDAP consumes
            if(Schema::hasColumn('users','ldap_unique_key')){
                $table->renameColumn('ldap_unique_key', 'import_identifier');
            } else {
                $table->string('import_identifier')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('import_identifier');
        });
    }
}
