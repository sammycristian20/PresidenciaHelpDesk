<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelephonyCallLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telephony_call_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('call_id', 100);
            $table->string('call_from', 50);
            $table->string('call_to', 50);
            $table->string('connecting_to', 50);
            $table->string('call_status');
            $table->text('notes')->nullable();
            $table->string('recording')->nullable();
            $table->integer('caller_user_id')->unsigned()->nullable();
            $table->foreign('caller_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('receiver_user_id')->unsigned()->nullable();
            $table->foreign('receiver_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('provider_id')->unsigned();
            $table->foreign('provider_id')->references('id')->on('telephony_provider_settings')->onDelete('restrict');
            $table->integer('call_ticket_id')->unsigned()->nullable();
            $table->foreign('call_ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->integer('intended_department_id')->unsigned()->nullable();
            $table->foreign('intended_department_id')->references('id')->on('department')->onDelete('cascade');
            $table->integer('intended_helptopic_id')->unsigned()->nullable();
            $table->foreign('intended_helptopic_id')->references('id')->on('help_topic')->onDelete('cascade');
            $table->boolean('job_dispatched')->default(0);
            $table->boolean('auto_conversion')->default(1);
            $table->timestamp('call_start_date')->nullable();
            $table->timestamp('call_end_date')->nullable();
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
        Schema::drop('telephony_call_logs');
    }
}
