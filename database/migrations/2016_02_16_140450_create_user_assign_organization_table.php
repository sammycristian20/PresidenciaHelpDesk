<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAssignOrganizationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_assign_organization', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('org_id')->unsigned()->nullable()->index('org_id');
            $table->integer('user_id')->unsigned()->nullable()->index('user_id');
            $table->string('role');
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
        Schema::drop('user_assign_organization');
    }
}
