<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emails', function(Blueprint $table){
            $table->string('fetching_encryption')->nullable(true)->change();
            $table->string('sending_encryption')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emails', function(Blueprint $table){
            $table->string('fetching_encryption')->nullable(false)->change();
            $table->string('sending_encryption')->nullable(false)->change();
        });
    }
}
