<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeDepartmentCannedResposneTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('department_canned_resposne', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('dept_id')->unsigned()->index('department_canned_resposne_dept_id_foreign');
			$table->integer('canned_id')->unsigned()->index('department_canned_resposne_canned_id_foreign');
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
		Schema::drop('department_canned_resposne');
	}

}
