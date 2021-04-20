<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOrganizationTablePhoneAndAddressNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('organization', function (Blueprint $table) {
        $table->string('phone')->nullable()->change();
        $table->string('address')->nullable()->change();
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
        $table->string('phone')->nullable(false)->change();
        $table->string('address')->nullable(false)->change();
      });
    }
}
