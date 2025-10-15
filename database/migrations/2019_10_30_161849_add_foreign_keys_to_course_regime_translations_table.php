<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCourseRegimeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('course_regime_translations', function(Blueprint $table)
		{
			$table->foreign('course_regimes_id')->references('id')->on('course_regimes')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
		Schema::table('course_regime_translations', function(Blueprint $table)
		{
			$table->dropForeign('course_regime_translations_course_regimes_id_foreign');
			$table->dropForeign('course_regime_translations_language_id_foreign');
		});
	}

}
