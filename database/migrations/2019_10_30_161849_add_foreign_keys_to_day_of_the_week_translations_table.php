<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDayOfTheWeekTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('day_of_the_week_translations', function(Blueprint $table)
		{
			$table->foreign('day_of_the_week_id', 'day_of_the_week_translations_ibfk_1')->references('id')->on('days_of_the_week')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('language_id', 'day_of_the_week_translations_ibfk_2')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('day_of_the_week_translations', function(Blueprint $table)
		{
			$table->dropForeign('day_of_the_week_translations_ibfk_1');
			$table->dropForeign('day_of_the_week_translations_ibfk_2');
		});
	}

}
