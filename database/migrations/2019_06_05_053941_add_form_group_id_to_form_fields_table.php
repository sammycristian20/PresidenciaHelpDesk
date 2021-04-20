<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormGroupIdToFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_fields', function (Blueprint $table) {
          $table->unsignedInteger('form_group_id')->nullable();

          // link foriegn key of form_group table to it
          $table->foreign('form_group_id')
               ->references('id')
               ->on('form_groups')
               ->onUpdate('cascade')
               ->onDelete('cascade');;
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
             $table->dropForeign('form_group_id');
             $table->dropColumn('form_group_id');
        });
    }
}
