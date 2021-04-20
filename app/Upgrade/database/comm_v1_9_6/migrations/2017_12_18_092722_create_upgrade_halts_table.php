<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeHaltsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('halts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('ticket_id');
			$table->dateTime('halted_at');
			$table->integer('time_used');
			$table->timestamps();
			$table->integer('halted_time')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('halts');
	}

}
