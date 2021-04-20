<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvailableForFetchAndLastFetchedAtColumnsToEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->boolean('available_for_fetch')->default(1)->nullable();
            $table->dateTime('last_fetched_at');
            $table->index('last_fetched_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('available_for_fetch');
            $table->dropColumn('last_fetched_at');
        });
    }
}
