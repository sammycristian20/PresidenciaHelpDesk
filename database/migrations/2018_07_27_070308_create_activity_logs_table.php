<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->integer('type_id')->nullable();
            $table->string('field');
            $table->integer('action_taker_id')->nullable();
            $table->string('action_taker_type');
            $table->string('initial_value');
            $table->string('final_value');
            $table->boolean('is_created');
            $table->boolean('is_updated');
            $table->boolean('is_deleted');
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
        Schema::dropIfExists('activity_logs');
    }
}
