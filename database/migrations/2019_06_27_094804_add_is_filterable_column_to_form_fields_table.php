<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsFilterableColumnToFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_fields', function (Blueprint $table) {
          // if form fields are filterable.
          // for eg. billing is not needed to be filterable
          $table->boolean('is_filterable')->default(true);
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
          $table->dropColumn('is_filterable');
        });
    }
}
