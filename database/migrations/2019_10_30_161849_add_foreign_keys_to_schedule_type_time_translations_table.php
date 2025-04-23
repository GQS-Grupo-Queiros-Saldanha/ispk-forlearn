<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToScheduleTypeTimeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schedule_type_time_translations', function(Blueprint $table)
		{
			$table->foreign('schedule_type_time_id', 'schedule_type_time_translations_ibfk_1')->references('id')->on('schedule_type_times')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('language_id', 'schedule_type_time_translations_ibfk_2')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('schedule_type_time_translations', function(Blueprint $table)
		{
			$table->dropForeign('schedule_type_time_translations_ibfk_1');
			$table->dropForeign('schedule_type_time_translations_ibfk_2');
		});
	}

}
