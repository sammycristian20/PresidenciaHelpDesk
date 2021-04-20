<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeBillTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable("bill_type"))
		{
			Schema::create('bill_type', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('type');
				$table->string('price');
				$table->timestamps();
			});
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bill_type');
	}

}
