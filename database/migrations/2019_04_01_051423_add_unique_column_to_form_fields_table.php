<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * NOTE: this migration has been added so that unique value can be stored in form_fields table
 * which will help reducing number of queries in migration from old forms to new forms.
 * 
 * This migration must be removed once 2.0.0 is released. This column is only used in test cases
 */
class AddUniqueColumnToFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_fields', function (Blueprint $table) {
          $table->string('unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_fields', function (Blueprint $table) {
          $table->dropColumn('unique');
        });
    }
}
