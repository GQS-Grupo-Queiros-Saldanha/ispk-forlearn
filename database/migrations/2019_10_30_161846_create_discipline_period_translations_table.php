<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDisciplinePeriodTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discipline_period_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('discipline_periods_id')->unsigned()->index('discipline_period_translations_discipline_periods_id_foreign');
			$table->integer('language_id')->unsigned()->index('discipline_period_translations_language_id_foreign');
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
		Schema::drop('discipline_period_translations');
	}

}
