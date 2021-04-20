<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->string('field');
            $table->string('relation');
            $table->string('value');
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
        Schema::dropIfExists('ticket_rules');
    }
}
