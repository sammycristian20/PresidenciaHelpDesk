<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->increments('id');
         
            $table->unsignedInteger('category_id')->nullable();
            
            $table->unsignedInteger('sort_order')->nullable();
                
            $table->string('title');
            
            $table->string('type');
            
            $table->boolean('required_for_agent');
            
            $table->boolean('required_for_user');
            
            $table->boolean('display_for_agent');
            
            $table->boolean('display_for_user');

            $table->boolean('default');
            
            $table->boolean('is_linked');
            
            $table->boolean('media_option');

            $table->string('api_endpoint')->nullable();
            
            $table->string('pattern')->nullable();

            $table->unsignedInteger('option_id')->nullable();

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
        Schema::dropIfExists('form_fields');
    }
}
