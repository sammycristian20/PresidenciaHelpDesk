<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePackageDepartmentLink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_department_link', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade')->onUpdate('restrict');
            $table->integer('department_id')->unsigned();
            $table->foreign('department_id')->references('id')->on('department')->onDelete('cascade')->onUpdate('restrict');
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
        Schema::dropIfExists('table_package_department_link');
    }
}
