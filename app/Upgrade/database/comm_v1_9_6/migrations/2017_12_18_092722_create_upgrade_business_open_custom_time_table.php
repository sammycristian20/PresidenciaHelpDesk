<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeBusinessOpenCustomTimeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('business_open_custom_time', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('business_schedule_id')->unsigned()->index('business_open_custom_time_business_schedule_id_foreign');
			$table->string('open_time');
			$table->string('close_time');
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
		Schema::drop('business_open_custom_time');
	}

}
