<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeEmailThreadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_threads', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('ticket_id');
			$table->integer('thread_id');
			$table->string('message_id');
			$table->integer('uid');
			$table->string('reference_id');
			$table->timestamps();
			$table->string('fetching_email');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('email_threads');
	}

}
