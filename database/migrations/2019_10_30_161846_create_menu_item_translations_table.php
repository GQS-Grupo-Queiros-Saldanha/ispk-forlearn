<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenuItemTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menu_item_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('menu_items_id')->unsigned()->index('menu_item_translations_menu_items_id');
			$table->integer('language_id')->unsigned()->index('menu_item_translations_language_id');
			$table->string('display_name', 191)->nullable();
			$table->string('description', 191)->nullable();
			$table->integer('version');
			$table->boolean('active')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('menu_item_translations');
	}

}
