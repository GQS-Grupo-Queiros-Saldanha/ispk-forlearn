<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMenuItemTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('menu_item_translations', function(Blueprint $table)
		{
			$table->foreign('language_id', 'menu_item_translations_ibfk_1')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('menu_items_id', 'menu_item_translations_ibfk_2')->references('id')->on('menu_items')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('menu_item_translations', function(Blueprint $table)
		{
			$table->dropForeign('menu_item_translations_ibfk_1');
			$table->dropForeign('menu_item_translations_ibfk_2');
		});
	}

}
