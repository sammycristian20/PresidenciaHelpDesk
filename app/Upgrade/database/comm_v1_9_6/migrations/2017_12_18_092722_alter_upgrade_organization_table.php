<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUpgradeOrganizationTable extends Migration
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
            $table->string('domain');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
