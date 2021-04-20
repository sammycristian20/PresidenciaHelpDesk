<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeLocationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('location', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->string('email');
			$table->string('phone');
			$table->string('address');
			$table->integer('status');
			$table->timestamps();
			$table->integer('is_default');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('location');
	}

}
