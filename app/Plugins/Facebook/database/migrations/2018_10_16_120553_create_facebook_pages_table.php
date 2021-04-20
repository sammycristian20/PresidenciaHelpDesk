<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app_id');
            $table->string('page_id');
            $table->string('page_name');
            $table->string('access_token');
            $table->integer('active')->default(0);
            $table->text('logo');
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
        //
    }
}
