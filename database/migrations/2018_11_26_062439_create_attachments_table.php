<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


/**
 * Migration for attachments table
 * @todo  Figure out how to create a column to store files in blob/medium blob to store files
 * in database using Laravel Schema not raw query.
 */
class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('file_size');
            $table->string('file_type', 100);
            $table->string('disposition');
            $table->string('driver', 30);
            $table->string('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
