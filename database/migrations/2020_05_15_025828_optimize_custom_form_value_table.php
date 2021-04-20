<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OptimizeCustomFormValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_form_value', function (Blueprint $table) {
            /**
             * Cannot run a normal migration from varchar to bigInt due to a bug in laravel
             * @see https://github.com/laravel/framework/issues/30539#issuecomment-559605145
             */
            $table->integer('custom_id')->charset(null)->collation(null)->change();
            $table->string('custom_type', 100)->change();
            $table->text('value')->change();
        });

        Schema::table('custom_form_value', function (Blueprint $table) {
            $table->index('custom_id');
            $table->index('custom_type');
            $table->foreign('form_field_id')->references('id')->on('form_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_form_value', function (Blueprint $table) {
            $table->dropIndex('custom_id');
            $table->dropIndex('custom_type');
            $table->dropForeign('form_field_id');
        });

        Schema::table('custom_form_value', function (Blueprint $table) {
            $table->string('custom_id')->change();
            $table->string('custom_type')->change();
        });
    }
}
