<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketSlaMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_sla_metas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("ticket_sla_id");
            $table->integer("priority_id");
            $table->string("respond_within");
            $table->string("resolve_within");
            $table->integer("business_hour_id");
            $table->boolean("send_email_notification")->default(true);
            $table->boolean("send_app_notification")->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_sla_metas');
    }
}
