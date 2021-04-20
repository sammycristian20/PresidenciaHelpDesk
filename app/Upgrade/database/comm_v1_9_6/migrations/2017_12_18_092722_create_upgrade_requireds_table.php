<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeRequiredsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('requireds', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('form')->nullable();
			$table->string('field')->nullable();
			$table->string('agent')->nullable();
			$table->string('client')->nullable();
			$table->integer('parent')->nullable();
			$table->string('option')->nullable();
			$table->string('label')->nullable();
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
		Schema::drop('requireds');
	}

}
