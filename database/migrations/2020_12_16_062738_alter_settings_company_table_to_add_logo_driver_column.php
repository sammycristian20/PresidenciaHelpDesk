<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSettingsCompanyTableToAddLogoDriverColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_company', function (Blueprint $table) {
            $table->string('logo_driver')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings_company', function (Blueprint $table) {
            $table->dropColumn('logo_driver');
        });
    }
}
