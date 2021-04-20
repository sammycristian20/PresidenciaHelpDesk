<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationDeptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_dept', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('org_id');
            $table->string('org_deptname');
            $table->integer('business_hours_id')->nullable();
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
        Schema::drop('organization_dept', function (Blueprint $table) {
        });
    }
}

