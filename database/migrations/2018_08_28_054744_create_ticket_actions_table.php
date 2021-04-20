<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_actions', function (Blueprint $table) {
            
            $table->increments('id');
            
            $table->unsignedInteger('reference_id')->nullable();
            
            $table->string('reference_type')->nullable();
            
            $table->string('field')->nullable();
            
            $table->string('value')->nullable();

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
        Schema::dropIfExists('ticket_actions');
    }
}
