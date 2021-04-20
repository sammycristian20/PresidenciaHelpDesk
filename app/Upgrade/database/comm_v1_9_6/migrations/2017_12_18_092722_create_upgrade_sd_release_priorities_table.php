<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeSdReleasePrioritiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('sd_release_priorities');
		Schema::create('sd_release_priorities', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
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
		Schema::drop('sd_release_priorities');
	}

}
