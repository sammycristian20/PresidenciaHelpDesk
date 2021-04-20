<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DropUnnecessaryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::disableForeignKeyConstraints();
		Schema::dropIfExists('custom_forms');
		Schema::dropIfExists('custom_form_fields');
		Schema::dropIfExists('field_values');
		Schema::dropIfExists('groups');
		Schema::dropIfExists('log_notification');
		Schema::dropIfExists('notification_types');
		Schema::dropIfExists('user_notification');
		Schema::enableForeignKeyConstraints();
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// nothing can stop me, I am all the way up
	}

}
