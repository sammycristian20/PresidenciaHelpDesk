<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeSlaTargetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sla_targets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('sla_id');
			$table->integer('priority_id');
			$table->string('respond_within');
			$table->string('resolve_within');
			$table->integer('business_hour_id');
			$table->integer('send_email');
			$table->integer('send_sms');
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
		Schema::drop('sla_targets');
	}

}
