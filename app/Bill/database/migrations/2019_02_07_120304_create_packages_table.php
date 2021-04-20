<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->boolean('status');
            $table->string('price');
            $table->string('validity')->nullable();
            $table->integer('allowed_tickets');
            $table->integer('display_order');
            $table->string('description');
            $table->string('package_pic')->nullable();
            $table->string('kb_link')->nullable();
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
        Schema::dropIfExists('packages');
    }
}
