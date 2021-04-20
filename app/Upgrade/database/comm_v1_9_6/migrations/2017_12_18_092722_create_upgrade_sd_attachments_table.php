<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeSdAttachmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('sd_attachments');
		Schema::create('sd_attachments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('saved')->unsigned()->index('sd_attachments_saved_foreign');
			$table->string('owner');
			$table->text('value', 65535);
			$table->string('type');
			$table->string('size');
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
		Schema::drop('sd_attachments');
	}

}
