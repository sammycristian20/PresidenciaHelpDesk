<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeTicketTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ticket_type', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('status');
			$table->string('type_desc');
			$table->integer('ispublic');
			$table->integer('is_default');
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
		Schema::drop('ticket_type');
	}

}
