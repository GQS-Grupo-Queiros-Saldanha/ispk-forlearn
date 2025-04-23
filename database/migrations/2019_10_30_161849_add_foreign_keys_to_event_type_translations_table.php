<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEventTypeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('event_type_translations', function(Blueprint $table)
		{
			$table->foreign('event_type_id', 'event_type_translations_ibfk_1')->references('id')->on('event_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('language_id', 'event_type_translations_ibfk_2')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('event_type_translations', function(Blueprint $table)
		{
			$table->dropForeign('event_type_translations_ibfk_1');
			$table->dropForeign('event_type_translations_ibfk_2');
		});
	}

}
