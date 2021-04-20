<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeRequiredFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('required_fields', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('form');
			$table->string('name');
			$table->integer('is_agent_required');
			$table->integer('is_client_required');
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
		Schema::drop('required_fields');
	}

}
