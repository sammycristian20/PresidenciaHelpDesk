<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIconColumnsToTicketFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_filters', function (Blueprint $table) {
            $table->string('icon_class', 30)->nullable();
            $table->string('icon_color', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_filters', function (Blueprint $table) {
            $table->dropColumn('icon_class');
            $table->dropColumn('icon_color');
        });
    }
}
