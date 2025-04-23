<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSpeHasDisciplinesHasModuleTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('spe_has_disciplines_has_module_translations', function(Blueprint $table)
		{
			$table->foreign('spe_has_disciplines_has_module_id', 'spe_has_disciplines_has_module_translations_ibfk_1')->references('id')->on('spe_has_disciplines_has_modules')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('language_id', 'spe_has_disciplines_has_module_translations_ibfk_2')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('spe_has_disciplines_has_module_translations', function(Blueprint $table)
		{
			$table->dropForeign('spe_has_disciplines_has_module_translations_ibfk_1');
			$table->dropForeign('spe_has_disciplines_has_module_translations_ibfk_2');
		});
	}

}
