<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValueMetaInTicketFilterMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // naming column value to value_meta, and then creating another column value, so that
        // existing data isn't lost
        Schema::table('ticket_filter_meta', function (Blueprint $table) {
            $table->renameColumn('value','value_meta');
        });

        Schema::table('ticket_filter_meta', function (Blueprint $table) {
            $table->longText('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_filter_meta', function (Blueprint $table) {
            $table->dropColumn('value');
        });

        Schema::table('ticket_filter_meta', function (Blueprint $table) {
            $table->renameColumn('value_meta','value');
        });
    }
}
