<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudyPlanEditionTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('study_plan_edition_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('study_plan_editions_id')->unsigned()->index('study_plan_edition_translations_study_plan_editions_id_foreign');
			$table->integer('language_id')->unsigned()->index('study_plan_edition_translations_language_id_foreign');
			$table->string('display_name', 191)->nullable();
			$table->string('description', 191)->nullable();
			$table->integer('version');
			$table->boolean('active')->nullable();
			$table->string('abbreviation', 191)->nullable();
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
		Schema::drop('study_plan_edition_translations');
	}

}
