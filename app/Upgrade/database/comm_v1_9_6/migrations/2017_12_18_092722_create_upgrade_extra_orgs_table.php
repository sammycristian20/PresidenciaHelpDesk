<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeExtraOrgsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('extra_orgs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('org_id');
			$table->string('key');
			$table->string('value');
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
		Schema::drop('extra_orgs');
	}

}
