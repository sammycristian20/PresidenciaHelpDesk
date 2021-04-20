<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomToCustomFormValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_form_value', function($table) {
          $table->dropColumn('ticket_id');
          $table->string('custom_id');
          $table->string('custom_type');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_form_value', function($table) {
            $table->dropColumn('custom_id');
            $table->dropColumn('custom_type');
        });
    }
}
