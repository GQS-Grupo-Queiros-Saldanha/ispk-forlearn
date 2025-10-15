<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDurationTypeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('duration_type_translations', function(Blueprint $table)
		{
			$table->foreign('duration_types_id')->references('id')->on('duration_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
		Schema::table('duration_type_translations', function(Blueprint $table)
		{
			$table->dropForeign('duration_type_translations_duration_types_id_foreign');
			$table->dropForeign('duration_type_translations_language_id_foreign');
		});
	}

}
