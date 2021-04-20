<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketSlaViolatedEscalateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_sla_violated_escalate', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ticket_id');
            $table->integer('sla_violated_escalate_id');
            $table->timestamp('trigger_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_sla_violated_escalate');
    }
}
