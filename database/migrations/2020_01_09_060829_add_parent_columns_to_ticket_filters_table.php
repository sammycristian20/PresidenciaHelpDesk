<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentColumnsToTicketFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_filters', function (Blueprint $table) {
            $table->integer('parent_id')->nullable();
            $table->string('parent_type')->nullable();
            $table->integer('user_id')->nullable()->unsigned()->change();
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
            $table->dropColumn('parent_id');
            $table->dropColumn('parent_type');
            $table->integer('user_id')->unsigned()->change();
        });
    }
}
