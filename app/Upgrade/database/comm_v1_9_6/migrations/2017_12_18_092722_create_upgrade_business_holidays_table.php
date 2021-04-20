<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeBusinessHolidaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('business_holidays', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('date');
			$table->integer('business_hours_id')->unsigned()->index('business_holidays_business_hours_id_foreign');
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
		Schema::drop('business_holidays');
	}

}
