<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomjsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customjs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('parameter', 50)->default(null);
            $table->string('fired_at', 50)->default('timeline');
            $table->longtext('script');
            $table->boolean('fire')->default(1);
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
        Schema::dropIfExists('customjs');
    }
}
