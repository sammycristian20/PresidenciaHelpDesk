<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('field_id')->nullable()->unsigned();
            $table->foreign('field_id')->references('id')->on('custom_form_fields');
            $table->integer('child_id')->nullable()->unsigned();
            $table->string('field_key')->nullable();
            $table->string('field_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_values');
    }
}
