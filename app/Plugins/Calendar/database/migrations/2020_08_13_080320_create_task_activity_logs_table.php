<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->string('causer_type')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index('log_name');
            $table->index(['subject_id', 'subject_type'], 'subject');
            $table->index(['causer_id', 'causer_type'], 'causer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_activity_logs');
    }
}
