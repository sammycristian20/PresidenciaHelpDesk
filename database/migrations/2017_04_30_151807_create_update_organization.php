<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateOrganization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('organization', function (Blueprint $table) {
            
             $table->string('client_Code');
             $table->string('phone1');
             $table->string('line_of_business');
             $table->string('relation_type');
             $table->string('branch');
             $table->string('fax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('organization', function (Blueprint $table) {
            $table->dropColumn('client_Code');
             $table->dropColumn('phone1');
             $table->dropColumn('line_of_business');
             $table->dropColumn('relation_type');
             $table->dropColumn('branch');
             $table->dropColumn('fax');
        });
    }
}
