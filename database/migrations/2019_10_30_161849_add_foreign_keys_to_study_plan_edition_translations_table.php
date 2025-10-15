<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToStudyPlanEditionTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('study_plan_edition_translations', function(Blueprint $table)
		{
			$table->foreign('language_id')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('study_plan_editions_id')->references('id')->on('study_plan_editions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('study_plan_edition_translations', function(Blueprint $table)
		{
			$table->dropForeign('study_plan_edition_translations_language_id_foreign');
			$table->dropForeign('study_plan_edition_translations_study_plan_editions_id_foreign');
		});
	}

}
