<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkedAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linked_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('attachment_id');
            $table->integer('category_id');
            $table->string('category_type');
            $table->timestamps();
            $table->foreign('attachment_id')->references('id')->on('attachments')
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
        Schema::table('linked_attachments', function (Blueprint $table) {
            $table->dropForeign(['attachment_id']);
        });
        Schema::dropIfExists('linked_attachments');
    }
}
