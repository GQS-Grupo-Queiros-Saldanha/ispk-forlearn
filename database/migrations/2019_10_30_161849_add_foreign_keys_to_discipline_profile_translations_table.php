<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDisciplineProfileTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('discipline_profile_translations', function(Blueprint $table)
		{
			$table->foreign('discipline_profiles_id')->references('id')->on('discipline_profiles')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
		Schema::table('discipline_profile_translations', function(Blueprint $table)
		{
			$table->dropForeign('discipline_profile_translations_discipline_profiles_id_foreign');
			$table->dropForeign('discipline_profile_translations_language_id_foreign');
		});
	}

}
