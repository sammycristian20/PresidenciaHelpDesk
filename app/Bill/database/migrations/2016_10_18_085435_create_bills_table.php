<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("bills")) {
            Schema::create('bills', function (Blueprint $table) {
                $table->increments('id');
                $table->string('level');
                $table->integer('model_id');
                $table->integer('agent');
                $table->integer('ticket_id');
                $table->string('hours');
                $table->string('billable');
                $table->string('amount_hourly')->nullable();
                $table->string('note');
                $table->timestamps();
            });
        }
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bills');
    }
}
