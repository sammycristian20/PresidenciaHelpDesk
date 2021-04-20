<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeBusinessScheduleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('business_schedule', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('business_hours_id')->unsigned()->index('business_schedule_business_hours_id_foreign');
			$table->string('days');
			$table->string('status');
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
		Schema::drop('business_schedule');
	}

}
