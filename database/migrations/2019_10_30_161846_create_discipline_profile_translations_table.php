<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDisciplineProfileTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discipline_profile_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('discipline_profiles_id')->unsigned()->index('discipline_profile_translations_discipline_profiles_id_foreign');
			$table->integer('language_id')->unsigned()->index('discipline_profile_translations_language_id_foreign');
			$table->string('display_name', 191)->nullable();
			$table->string('description', 191)->nullable();
			$table->integer('version');
			$table->boolean('active')->nullable();
			$table->string('abbreviation', 191)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('discipline_profile_translations');
	}

}
