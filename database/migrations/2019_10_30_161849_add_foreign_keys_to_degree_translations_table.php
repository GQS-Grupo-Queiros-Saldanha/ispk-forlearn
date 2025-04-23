<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDegreeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('degree_translations', function(Blueprint $table)
		{
			$table->foreign('degrees_id')->references('id')->on('degrees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
		Schema::table('degree_translations', function(Blueprint $table)
		{
			$table->dropForeign('degree_translations_degrees_id_foreign');
			$table->dropForeign('degree_translations_language_id_foreign');
		});
	}

}
