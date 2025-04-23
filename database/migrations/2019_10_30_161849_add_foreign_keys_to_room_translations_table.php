<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToRoomTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('room_translations', function(Blueprint $table)
		{
			$table->foreign('room_id', 'room_translations_ibfk_1')->references('id')->on('rooms')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('language_id', 'room_translations_ibfk_2')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('room_translations', function(Blueprint $table)
		{
			$table->dropForeign('room_translations_ibfk_1');
			$table->dropForeign('room_translations_ibfk_2');
		});
	}

}
