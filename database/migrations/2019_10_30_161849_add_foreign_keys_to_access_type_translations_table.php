<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAccessTypeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('access_type_translations', function(Blueprint $table)
		{
			$table->foreign('access_type_id')->references('id')->on('access_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
		Schema::table('access_type_translations', function(Blueprint $table)
		{
			$table->dropForeign('access_type_translations_access_type_id_foreign');
			$table->dropForeign('access_type_translations_language_id_foreign');
		});
	}

}
