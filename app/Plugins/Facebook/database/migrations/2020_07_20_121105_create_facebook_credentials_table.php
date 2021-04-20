<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_credentials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('page_id');
            $table->string('page_name');
            $table->text('page_access_token');
            $table->integer('new_ticket_interval')->default(5);
            $table->integer('active')->default(1);
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
        Schema::dropIfExists('facebook_credentials');
    }
}
