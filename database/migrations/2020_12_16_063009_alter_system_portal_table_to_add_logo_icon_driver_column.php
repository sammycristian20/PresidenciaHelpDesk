<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSystemPortalTableToAddLogoIconDriverColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_portal', function (Blueprint $table) {
            $table->string('logo_icon_driver')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_portal', function (Blueprint $table) {
            $table->dropColumn('logo_icon_driver');
        });
    }
}
