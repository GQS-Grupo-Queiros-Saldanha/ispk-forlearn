<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDisciplineCurriculaTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discipline_curricula_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('discipline_curricula_id')->unsigned();
			$table->integer('language_id')->unsigned()->index('discipline_curricula_translations_language_id_foreign');
			$table->integer('version')->nullable();
			$table->boolean('active')->nullable();
			$table->text('presentation', 16777215)->nullable();
			$table->text('bibliography', 16777215)->nullable();
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
		Schema::drop('discipline_curricula_translations');
	}

}
