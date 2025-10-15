<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToScheduleTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schedule_translations', function(Blueprint $table)
		{
			$table->foreign('schedule_id', 'schedule_translations_ibfk_1')->references('id')->on('schedules')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('language_id', 'schedule_translations_ibfk_2')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('schedule_translations', function(Blueprint $table)
		{
			$table->dropForeign('schedule_translations_ibfk_1');
			$table->dropForeign('schedule_translations_ibfk_2');
		});
	}

}
