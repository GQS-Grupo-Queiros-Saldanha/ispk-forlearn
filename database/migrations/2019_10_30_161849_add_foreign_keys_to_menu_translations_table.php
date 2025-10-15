<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMenuTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('menu_translations', function(Blueprint $table)
		{
			$table->foreign('language_id', 'menu_translations_ibfk_1')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('menus_id', 'menu_translations_ibfk_2')->references('id')->on('menus')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('menu_translations', function(Blueprint $table)
		{
			$table->dropForeign('menu_translations_ibfk_1');
			$table->dropForeign('menu_translations_ibfk_2');
		});
	}

}
