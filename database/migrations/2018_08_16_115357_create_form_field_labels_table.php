<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormFieldLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_field_labels', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('language');
            
            $table->string('label');
            
            $table->string('meant_for')->nullable();//agent. client or null

            $table->unsignedInteger('labelable_id')->nullable();
            
            $table->string('labelable_type')->nullable();
            
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
        Schema::dropIfExists('form_field_labels');
    }
}
