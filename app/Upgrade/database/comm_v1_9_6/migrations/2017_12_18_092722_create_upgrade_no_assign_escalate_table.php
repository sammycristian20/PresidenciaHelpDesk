<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeNoAssignEscalateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('no_assign_escalate', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('sla_plan');
			$table->string('escalate_time');
			$table->string('escalate_type');
			$table->string('escalate_person');
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
		Schema::drop('no_assign_escalate');
	}

}
