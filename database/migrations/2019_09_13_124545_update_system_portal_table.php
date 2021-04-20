<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSystemPortalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('system_portal', function (Blueprint $table) {

            $table->string('client_header_color',10)->nullable()->change();
            $table->string('client_button_color',10)->nullable()->change();
            $table->string('client_button_border_color',10)->nullable()->change();
            $table->string('client_input_field_color',10)->nullable()->change();
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
            $table->string('client_header_color')->change();
            $table->string('client_button_color')->change();
            $table->string('client_button_border_color')->change();
            $table->string('client_input_field_color')->change();
        });
    }
}
