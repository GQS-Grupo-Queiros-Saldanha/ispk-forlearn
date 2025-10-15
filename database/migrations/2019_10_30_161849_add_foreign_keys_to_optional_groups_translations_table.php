<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOptionalGroupsTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('optional_groups_translations', function(Blueprint $table)
		{
			$table->foreign('language_id')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('optional_groups_id')->references('id')->on('optional_groups')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('optional_groups_translations', function(Blueprint $table)
		{
			$table->dropForeign('optional_groups_translations_language_id_foreign');
			$table->dropForeign('optional_groups_translations_optional_groups_id_foreign');
		});
	}

}
