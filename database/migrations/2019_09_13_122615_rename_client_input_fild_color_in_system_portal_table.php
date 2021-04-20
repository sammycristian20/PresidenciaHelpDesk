<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameClientInputFildColorInSystemPortalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_portal', function (Blueprint $table) {
            $table->renameColumn('client_input_fild_color', 'client_input_field_color');
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
            $table->renameColumn('client_input_fild_color', 'client_input_field_color');
        });
    }
}
