<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeLicensesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('licenses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('licenses_type');
			$table->string('org_id');
			$table->string('licenses_key');
			$table->string('activated_on');
			$table->string('start_date');
			$table->string('expires_on');
			$table->string('comments');
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
		Schema::drop('licenses');
	}

}
