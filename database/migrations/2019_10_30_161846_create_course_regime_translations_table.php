<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseRegimeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_regime_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('language_id')->unsigned()->index('course_regime_translations_language_id_foreign');
			$table->integer('course_regimes_id')->unsigned()->index('course_regime_translations_course_regimes_id_foreign');
			$table->string('abbreviation', 191)->nullable();
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
		Schema::drop('course_regime_translations');
	}

}
