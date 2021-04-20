<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStatusDataTypeCommonSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('common_settings', function (Blueprint $table) {
            $table->boolean('status')->nullable()->charset('')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('common_settings', function (Blueprint $table) {
            $table->string('status', 255)->change();
        });
    }
}
