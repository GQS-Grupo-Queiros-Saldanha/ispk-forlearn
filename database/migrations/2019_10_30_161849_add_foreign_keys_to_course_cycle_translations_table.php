<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCourseCycleTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('course_cycle_translations', function(Blueprint $table)
		{
			$table->foreign('course_cycles_id')->references('id')->on('course_cycles')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
		Schema::table('course_cycle_translations', function(Blueprint $table)
		{
			$table->dropForeign('course_cycle_translations_course_cycles_id_foreign');
			$table->dropForeign('course_cycle_translations_language_id_foreign');
		});
	}

}
