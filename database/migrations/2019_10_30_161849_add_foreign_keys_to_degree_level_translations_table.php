<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDegreeLevelTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('degree_level_translations', function(Blueprint $table)
		{
			$table->foreign('degree_levels_id')->references('id')->on('degree_levels')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('language_id')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('degree_level_translations', function(Blueprint $table)
		{
			$table->dropForeign('degree_level_translations_degree_levels_id_foreign');
			$table->dropForeign('degree_level_translations_language_id_foreign');
		});
	}

}
