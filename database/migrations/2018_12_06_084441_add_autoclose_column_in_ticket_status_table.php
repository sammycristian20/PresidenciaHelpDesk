<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutocloseColumnInTicketStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_status', function (Blueprint $table) {
            $table->unsignedInteger('auto_close')->nullable();
            $table->foreign('auto_close')->references('id')->on('workflow_close')
            ->onDelete('cascade')->onUpdate('restrict');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_status', function (Blueprint $table) {
            $table->dropForeign(['auto_close']);
        });
    }
}
