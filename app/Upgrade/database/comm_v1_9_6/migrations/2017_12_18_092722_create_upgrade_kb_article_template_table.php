<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpgradeKbArticleTemplateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('kb_article_template', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->boolean('status');
			$table->text('description', 65535);
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
		Schema::drop('kb_article_template');
	}

}
