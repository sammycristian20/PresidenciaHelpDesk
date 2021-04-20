<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeApprovalMetasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('approval_metas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('approval_id');
			$table->integer('ticket_id')->nullable();
			$table->string('option');
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
		Schema::drop('approval_metas');
	}

}
