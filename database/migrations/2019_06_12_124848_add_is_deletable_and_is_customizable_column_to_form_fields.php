<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDeletableAndIsCustomizableColumnToFormFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_fields', function (Blueprint $table) {
          // if this field is deletable in form builder
          $table->boolean('is_deletable')->default(true);

          // if field is allowed to be customized
          // If this is false, admin won't be able to add options or change
          // API endpoint, but they will be able to change the label
          $table->boolean('is_customizable')->default(true);

          // if it should come in workflow-listener-sla or not
          $table->boolean('is_observable')->default(true);
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
          $table->dropColumn('is_deletable');
          $table->dropColumn('is_customizable');
          $table->dropColumn('is_observable');
        });
    }
}
