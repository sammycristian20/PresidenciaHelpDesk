<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeSdTicketRelationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('sd_ticket_relation');	
		Schema::create('sd_ticket_relation', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('ticket_id');
			$table->string('owner');
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
		Schema::drop('sd_ticket_relation');
	}

}
