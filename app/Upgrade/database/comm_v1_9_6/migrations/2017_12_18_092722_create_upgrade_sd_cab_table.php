<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeSdCabTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('sd_cab');
		Schema::create('sd_cab', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('head')->unsigned()->nullable()->index('sd_cab_head_foreign');
			$table->string('approvers')->nullable();
			$table->integer('aproval_mandatory');
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
		Schema::drop('sd_cab');
	}

}
