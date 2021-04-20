<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptinToTicketSource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('ticket_source', function (Blueprint $table) {
            $table->longText('description')->default(null)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('ticket_source', function (Blueprint $table) {
            $table->dropColumn('description')->default(null)->nullable();
        });
    }
}
