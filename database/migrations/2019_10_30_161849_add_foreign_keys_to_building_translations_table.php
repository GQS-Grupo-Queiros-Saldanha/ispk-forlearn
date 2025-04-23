<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBuildingTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('building_translations', function(Blueprint $table)
		{
			$table->foreign('building_id', 'building_translations_ibfk_1')->references('id')->on('buildings')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('language_id', 'building_translations_ibfk_2')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('building_translations', function(Blueprint $table)
		{
			$table->dropForeign('building_translations_ibfk_1');
			$table->dropForeign('building_translations_ibfk_2');
		});
	}

}
