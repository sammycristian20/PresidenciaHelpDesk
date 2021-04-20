<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTableNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 80);
            $table->text('description');
            $table->bigInteger('user_id')->nullable();
            $table->boolean('is_public');
            $table->string('type');
            $table->string("icon_class", 50);
            $table->string('category', 30);
            $table->string('view_url')->nullable();
            $table->string('export_url')->nullable();
            $table->boolean('is_default')->default(false);
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
        Schema::dropIfExists('reports');
    }
}
