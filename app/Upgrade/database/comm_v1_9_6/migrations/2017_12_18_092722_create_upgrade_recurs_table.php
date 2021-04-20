<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeRecursTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('recurs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('interval');
			$table->string('delivery_on');
			$table->dateTime('start_date')->nullable();
			$table->dateTime('end_date')->nullable();
			$table->dateTime('last_execution')->nullable();
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
		Schema::drop('recurs');
	}

}
