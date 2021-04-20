<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserTableCorrectVariousColumnsDatatype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('active')->default(1)->change();
            $table->boolean('is_delete')->default(0)->change();
            $table->string('ext')->nullable()->change();
            $table->string('internal_note')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('active')->default(NULL)->change();
            $table->integer('is_delete')->default(NULL)->change();
            $table->string('ext')->nullable(false)->change();
            $table->string('internal_note')->nullable(false)->change();
        });
    }
}
